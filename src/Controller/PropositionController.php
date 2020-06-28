<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Proposition;
use App\Form\CommentType;
use App\Form\PropositionType;
use App\Repository\CommentRepository;
use App\Repository\PropositionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("one-up/proposition")
 */
class PropositionController extends AbstractController
{
    /**
     * @Route("/", name="proposition_index", methods={"GET"})
     * @param PropositionRepository $propositionRepository
     * @return Response
     */
    public function index(PropositionRepository $propositionRepository): Response
    {
        return $this->render('proposition/index.html.twig', [
            'propositions' => $propositionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="proposition_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $user = $this->getUser();
        $proposition = new Proposition();
        $form = $this->createForm(PropositionType::class, $proposition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $proposition->setAuthor($user);
            $entityManager->persist($proposition);
            $entityManager->flush();

            $this->addFlash('success','Votre proposition a bien été soumise!');
            return $this->redirectToRoute('homepage');
        }



        return $this->render('proposition/new.html.twig', [
            'proposition' => $proposition,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="proposition_show", methods={"GET","POST"})
     * @param Request $request
     * @param CommentRepository $commentRepository
     * @param PropositionRepository $propositionRepository
     * @param $slug
     * @return Response
     */
    public function show(Request $request, CommentRepository $commentRepository,PropositionRepository $propositionRepository,$slug): Response
    {

        $author  = $this->getUser();
        $proposition = $propositionRepository->findOneBy(['slug'=>$slug]);
        $commentAuthor = $commentRepository->findOneBy(['author'=>$author,'proposition'=>$proposition]);
        $comment = $commentRepository->findOneBy(['id'=>$commentAuthor]);
        $comments = $commentRepository->findBy(['proposition'=>$proposition],['createdAt' => 'desc']);

        if(!$comment) {
            $comment = new Comment();
        }

        $form = $this->createForm(CommentType::class, $comment);
        $entityManager = $this->getDoctrine()->getManager();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($comment){
                if($commentAuthor === $comment){
                    $entityManager->persist($comment);
                    $entityManager->flush();
                } else {
                    $comment->setProposition($proposition);
                    $comment->setAuthor($author);
                    $entityManager->persist($comment);
                    $entityManager->flush();
                }
            }

        }
        return $this->render('proposition/show.html.twig', [
            'proposition' => $proposition,
            'form' => $form->createView(),
            'comments' => $comments,
            'comment'=>$comment
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="proposition_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Proposition $proposition
     * @return Response
     */
    public function edit(Request $request, Proposition $proposition): Response
    {
        $form = $this->createForm(PropositionType::class, $proposition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('proposition_index');
        }

        return $this->render('proposition/edit.html.twig', [
            'proposition' => $proposition,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="proposition_delete", methods={"DELETE"})
     * @param Request $request
     * @param Proposition $proposition
     * @return Response
     */
    public function delete(Request $request, Proposition $proposition): Response
    {
        if ($this->isCsrfTokenValid('delete'.$proposition->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($proposition);
            $entityManager->flush();
        }

        return $this->redirectToRoute('proposition_index');
    }
}
