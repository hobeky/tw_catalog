<?php

namespace App\Controller;

use App\Entity\Carousel;
use App\Entity\Image;
use App\Form\CarouselType;
use App\Repository\CarouselRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/carousel", name="carousel.")
 */
class CarouselController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(CarouselRepository $carouselRepository): Response
    {
        return $this->render('carousel/index.html.twig', [
            'carousels' => $carouselRepository->findBy([], ['indexImage' => 'ASC']),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $carousel = new Carousel();
        $form = $this->createForm(CarouselType::class, $carousel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $image = new Image();
            $image->setFile($form->get('idImage')->getData());
            $carousel->setIdImage($image);
            $carousel->setIndexImage($entityManager->getRepository(Carousel::class)->countAll());
            $carousel->setAddedAt(new \DateTime('now'));

            $entityManager->persist($carousel);
            $entityManager->flush();

            return $this->redirectToRoute('carousel.index');
        }

        return $this->render('carousel/new.html.twig', [
            'carousel' => $carousel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reorder}", name="reorder", methods={"GET"})
     */
    public function reorder(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($request->get('order') as $order => $id) {
            $carousel = $em->getRepository(Carousel::class)->find($id);
            $carousel->setIndexImage($order);
            $em->persist($carousel);
        }
        $em->flush();

        return $this->json('OK');
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Carousel $carousel): Response
    {
        return $this->render('carousel/show.html.twig', [
            'carousel' => $carousel,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Carousel $carousel): Response
    {
        $form = $this->createForm(CarouselType::class, $carousel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($carousel->getIdImage());
            $image = new Image();
            $image->setFile($form->get('idImage')->getData());
            $carousel->setIdImage($image);
            $em->flush();

            return $this->redirectToRoute('carousel.index');
        }

        return $this->render('carousel/edit.html.twig', [
            'carousel' => $carousel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Carousel $carousel): Response
    {
        if ($this->isCsrfTokenValid('delete' . $carousel->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($carousel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('carousel.index');
    }
}
