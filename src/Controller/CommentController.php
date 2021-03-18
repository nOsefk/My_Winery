<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/comments", name="comments")
     * @param CommentRepository $repository
     * @return Response
     */
    public function index(CommentRepository $repository): Response
    {
        $comments = $repository->findAll();

        return $this->render('comment/index.html.twig', [
            'comments' => $comments,
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     * @Route("/comments/add", name="add_com")
     */
    public function add(Request $request, EntityManagerInterface $manager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            #$slugify = new Slugify();
            #$comment->setSlug($slugify->slugify($comment->getName()));
            $comment->setUser($this->getUser());
            $comment->setDate(new \DateTime('now'));
            #$comment->setProduct($this->getProduct());
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le commentaire a été correctement ajouté'
            );
            return $this->redirectToRoute('comments');
        }
        return $this->render('comment/form.html.twig', [
            'commentForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/comments/edit/{id}", name="update_com")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Comment $comment
     * @return Response
     */
    public function update(Request $request, EntityManagerInterface $manager, Comment $comment): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            #$slugify = new Slugify();
            #$comment->setSlug($slugify->slugify($comment->getName()));
            $manager->flush();
            return $this->redirectToRoute('comments');
        }
        return $this->render('comment/form.html.twig', [
            'commentForm' => $form->createView()
        ]);

    }

    /**
     * @Route("/comments/delete/{id}", name="del_com")
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Comment $comment, EntityManagerInterface $manager): Response {
        $manager->remove($comment);
        $manager->flush();
        return $this->redirectToRoute('comments');
    }
}
