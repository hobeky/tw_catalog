<?php

namespace App\Controller;

use App\Entity\Image;
use Nzo\UrlEncryptorBundle\Annotations\ParamDecryptor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/image", name="image.")
 */
class ImageController extends AbstractController
{
    /**
     * @ParamDecryptor(params={"id"})
     * @Route("/upload/{id}/{size}", name="db", defaults={"size"=""})
     */
    public function uploaded(Image $image, string $size)
    {
        if ($size == '') {
            $path = $image->getFile()->getRealPath();
        } else {
            $path = $image->getFile()->getRealPath() . '_' . $size;
        }

        $content = file_get_contents($path);

        $headers = array(
            'Content-Type' => 'image/' . $image->getFile()->guessExtension(),
            'Content-Disposition' => 'inline; filename="' . $image->getName() . '"');
        return new Response($content, 200, $headers);
    }

    /**
     * @Route("/remove_image/{id}", name="remove")
     *
     * @param Request $request
     * @param Image $image
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeImage(Request $request, Image $image)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($image);
        $manager->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}
