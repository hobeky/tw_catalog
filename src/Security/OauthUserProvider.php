<?php

namespace App\Security;

use App\Entity\User;
use App\Services\HwiBundleFixUserProvider;
use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\EntityUserProvider;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;

class OauthUserProvider extends HwiBundleFixUserProvider implements AccountConnectorInterface
{
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();

        if (!isset($this->properties[$resourceOwnerName])) {
            throw new \RuntimeException(sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName));
        }

        $serviceName = $response->getResourceOwner()->getName();
        $setterId = 'set' . ucfirst($serviceName) . 'ID';
        $setterAccessToken = 'set' . ucfirst($serviceName) . 'AccessToken';

        $username = $response->getUsername();
        if (null === $user = $this->findUser([$this->properties[$resourceOwnerName] => $username])) {
            $user = new User();
            $user->setEmail($response->getEmail());
            $user->setIsVerified(false);
            $user->setFacebookId('');
            $user->setGoogleId('');
            $user->$setterId($username);
            $user->$setterAccessToken($response->getAccessToken());

            $this->em->persist($user);
            $this->em->flush();

            return $user;
        }

        $user->$setterAccessToken($response->getAccessToken());

        return $user;
    }

    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Expected an instance of App\Model\User, but got "%s".', get_class($user)));
        }

        $property = $this->getProperty($response);
        $username = $response->getUsername();

        if (null !== $previousUser = $this->registry->getRepository(User::class)->findOneBy(array($property => $username))) {
            // 'disconnect' previously connected users
            $this->disconnect($previousUser, $response);
        }


        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';

        $user->$setter($response->getAccessToken());

        $this->updateUser($user, $response);
    }

    protected function getProperty(UserResponseInterface $response)
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();

        if (!isset($this->properties[$resourceOwnerName])) {
            throw new \RuntimeException(sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName));
        }

        return $this->properties[$resourceOwnerName];
    }

    public function disconnect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $accessor = PropertyAccess::createPropertyAccessor();

        $accessor->setValue($user, $property, null);

        $this->updateUser($user, $response);
    }

    private function updateUser(UserInterface $user, UserResponseInterface $response)
    {
        $user->setEmail($response->getEmail());
        $user->setIsVerified(true);
        // TODO: Add more fields?!

        $this->em->persist($user);
        $this->em->flush();
    }
}