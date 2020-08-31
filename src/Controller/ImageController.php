<?php

namespace App\Controller;

use App\Entity\Image;
use Nzo\UrlEncryptorBundle\Annotations\ParamDecryptor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/image", name="image.")
 */
class ImageController extends AbstractController
{
    /**
     * @ParamDecryptor(params={"id"})
     * @Route("/upload/{id}", name="db")
     */
    public function uploaded(Image $image)
    {
        $content = file_get_contents($image->getFile()->getRealPath());

        $headers = array(
            'Content-Type' => 'image/' . $image->getFile()->guessExtension(),
            'Content-Disposition' => 'inline; filename="' . $image->getName() . '"');
        return new Response($content, 200, $headers);
    }
}
