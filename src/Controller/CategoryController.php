<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
#use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/categories", name="categories")
     * @param CategoryRepository $repository
     * @return Response
     */
    public function index(CategoryRepository $repository): Response
    {
        $categories = $repository->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     * @Route("/add_cat", name="add_cat")
     */
    public function add(Request $request, EntityManagerInterface $manager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            #$slugify = new Slugify();
            #$category->setSlug($slugify->slugify($category->getName()));

            $manager->persist($category);
            $manager->flush();

            $this->addFlash(
                'success',
                'La catégorie a été correctement ajoutée'
            );
            return $this->redirectToRoute('categories');
        }
        return $this->render('category/form.html.twig', [
            'categoryForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/categories/edit/{id}", name="update_cat")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Category $category
     * @return Response
     */
    public function update(Request $request, EntityManagerInterface $manager, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            #$slugify = new Slugify();
            #$category->setSlug($slugify->slugify($category->getName()));
            $manager->flush();
            return $this->redirectToRoute('categories');
        }
        return $this->render('category/form.html.twig', [
            'categoryForm' => $form->createView()
        ]);

    }

    /**
     * @Route("/categories/delete/{id}", name="del_cat")
     * @param Category $category
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Category $category, EntityManagerInterface $manager): Response {
        $manager->remove($category);
        $manager->flush();
        return $this->redirectToRoute('categories');
    }
}
