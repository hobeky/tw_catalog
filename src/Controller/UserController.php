<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/user", name="user.")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(UserRepository $userRepository)
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll()
        ]);
    }

    /**
     * @Route("/verify_toggle/{id}", name="verifyToggle")
     */
    public function verifiedToggle(Request $request, $id, EmailVerifier $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);

        if (!$user->isVerified()) {
            $mailer->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('support@tw-catalog.com', 'TW Catalog'))
                    ->to($user->getEmail())
                    ->subject('Váš prístup bol schválený.')
                    ->htmlTemplate('registration/activate_account.hmtl.twig')
            );
        }

        $user->setIsVerified(!$user->isVerified());
        $em->persist($user);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}
