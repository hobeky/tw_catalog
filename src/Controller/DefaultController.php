<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Nzo\UrlEncryptorBundle\Annotations\ParamDecryptor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="default.")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $product = $this->em()->getRepository(Product::class);
        return $this->render('default/index.html.twig', [
            'menuOpen' => true,
            'carouselProducts' => $product->findBy([], ['createdAt' => 'DESC'],3),
            'latestProducts' => $product->findBy([], ['createdAt' => 'DESC'],12)
        ]);
    }

    /**
     * @ParamDecryptor(params={"id"})
     * @Route("/view/{id}", name="show")
     */
    public function show(Product $product)
    {
        return $this->render('default/show.html.twig', [
            'product' => $product
        ]);
    }

    private function em(): ObjectManager
    {
        return $this->getDoctrine()->getManager();
    }
}
