<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\RegionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/", name="comment_index", methods={"GET"})
     */
    public function index(CommentRepository $commentRepository, RegionRepository $regionRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
            'regions' => $regionRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="comment_new", methods={"GET","POST"})
     * @IsGranted ("ROLE_USER")
     */
    public function new(Request $request, RegionRepository $regionRepository, CategoryRepository $categoryRepository): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('comment_index');
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
            'categories' => $categoryRepository->findAll(),
            'regions' => $regionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="comment_show", methods={"GET"})
     * @param Comment $comment
     * @return Response
     */
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="comment_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Comment $comment
     * @IsGranted ("ROLE_USER")
     * @return Response
     */
    public function edit(Request $request, Comment $comment, RegionRepository $regionRepository, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_show', ['id'=>$comment->getProduct()->getId()]);
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
            'categories' => $categoryRepository->findAll(),
            'regions' => $regionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="comment_delete", methods={"DELETE"})
     * @param Request $request
     * @param Comment $comment
     * @IsGranted ("ROLE_ADMIN")
     * @return Response
     */
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('comment_index');
    }
}
