<?php

namespace App\Twig;

use App\Entity\Image;
use Nzo\UrlEncryptorBundle\UrlEncryptor\UrlEncryptor;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MiscExtension extends AbstractExtension
{
    private RouterInterface $router;
    private UrlEncryptor $encryptor;
    private Package $package;

    public function __construct(RouterInterface $router, UrlEncryptor $encryptor)
    {
        $this->router = $router;
        $this->encryptor = $encryptor;
        $this->package = new Package(new StaticVersionStrategy('v1'));
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('sriHash', [$this, 'sriHash']),
            new TwigFilter('image', [$this, 'image']),
        ];
    }

    public function getFunctions(): array
    {
        return [
        ];
    }

    public function sriHash(string $link): string
    {
        return 'sha384-' . base64_encode(hash('sha384', file_get_contents(__DIR__ . '/../../public' . $link), true));
    }


    /**
     * @param Image|false $image
     * @param string $size
     * @return string
     */
    public function image($image, string $size = ''): string
    {
        if (!$image) {
            return $this->package->getUrl('asset/user/images/no_image.png');
        }
        return $this->router->generate('image.db', ['id' => $this->encryptor->encrypt($image->getId()), 'size' => $size]);
    }

}
