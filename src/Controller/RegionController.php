<?php

namespace App\Controller;

use App\Entity\Region;
use App\Form\RegionType;
use App\Repository\CategoryRepository;
use App\Repository\RegionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/region")
 */
class RegionController extends AbstractController
{
    /**
     * @Route("/", name="region_index", methods={"GET"})
     * @param RegionRepository $regionRepository
     * @return Response
     */
    public function index(RegionRepository $regionRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('region/index.html.twig', [
            'regions' => $regionRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="region_new", methods={"GET","POST"})
     * @param Request $request
     * @IsGranted ("ROLE_ADMIN")
     * @return Response
     */
    public function new(Request $request, RegionRepository $regionRepository, CategoryRepository $categoryRepository): Response
    {
        $region = new Region();
        $form = $this->createForm(RegionType::class, $region);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($region);
            $entityManager->flush();

            return $this->redirectToRoute('region_index');
        }

        return $this->render('region/new.html.twig', [
            'region' => $region,
            'form' => $form->createView(),
            'regions' => $regionRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="region_show", methods={"GET"})
     * @param Region $region
     * @return Response
     */
    public function show(Region $region, RegionRepository $regionRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('region/show.html.twig', [
            'region' => $region,
            'regions' => $regionRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="region_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Region $region
     * @IsGranted ("ROLE_ADMIN")
     * @return Response
     */
    public function edit(Request $request, Region $region, RegionRepository $regionRepository, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(RegionType::class, $region);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('region_index');
        }

        return $this->render('region/edit.html.twig', [
            'region' => $region,
            'form' => $form->createView(),
            'regions' => $regionRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="region_delete", methods={"DELETE"})
     * @param Request $request
     * @param Region $region
     * @return Response
     * @IsGranted ("ROLE_SUPER")
     *
     */
    public function delete(Request $request, Region $region): Response
    {
        if ($this->isCsrfTokenValid('delete'.$region->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($region);
            $entityManager->flush();
        }

        return $this->redirectToRoute('region_index');
    }
}
