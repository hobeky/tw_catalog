<?php

namespace App\Controller;

use App\Entity\Carousel;
use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Nzo\UrlEncryptorBundle\Annotations\ParamDecryptor;
use Nzo\UrlEncryptorBundle\UrlEncryptor\UrlEncryptor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        $carousel = $this->em()->getRepository(Carousel::class)->findVisibleCarousels();

        return $this->render('default/index.html.twig', [
            'menuOpen' => true,
            'carouselProducts' => $carousel,
            'latestProducts' => $product->findBy([], ['createdAt' => 'DESC'],12),
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

    /**
     * @ParamDecryptor(params={"id"})
     * @Route("/category/{id}/{page}", name="category", requirements={"page"="\d+"})
     */
    public function category(Category $category, int $page = 0)
    {
        $product = $this->em()->getRepository(Product::class)->findBy([
            'category' => $category,
        ], [], $_ENV['PAGE_LIMIT'], $_ENV['PAGE_LIMIT'] * $page);

        return $this->render('default/category.html.twig', [
            'products' => $product,
            'total' => $this->em()->getRepository(Product::class)->totalInCategory($category),
            'page' => $page,
            'category' => $category
        ]);
    }

    /**
     * @Route("/quickview", name="quickview")
     */
    public function quickview(Request $request, UrlEncryptor $encryptor)
    {
        $product = $this->em()->getRepository(Product::class)->find($encryptor->decrypt($request->get("code")));

        return $this->render('default/quickview.html.twig', [
            'product' => $product
        ]);
    }

    private function em(): ObjectManager
    {
        return $this->getDoctrine()->getManager();
    }
}
