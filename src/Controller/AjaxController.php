<?php

namespace App\Controller;

use App\Entity\PostLike;
use App\Entity\Proposition;
use App\Repository\PostLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("one-up")
 */

class AjaxController extends AbstractController
{
    /**
     * @Route("/post/like", name="post_like")
     * @param PostLikeRepository $postLikeRepository
     * @return Response
     */
    public function index(PostLikeRepository $postLikeRepository)
    {
        return $this->render('post_like/index.html.twig', [
            'postLikes' => $postLikeRepository->findAll()
        ]);
    }

    /**
     * @Route("/like/proposition/{id}", name="like_user")
     * @param PostLikeRepository $postLikeRepository
     * @param EntityManagerInterface $entityManager
     * @param Proposition $proposition
     * @return JsonResponse
     */
    public function postLike(PostLikeRepository $postLikeRepository,EntityManagerInterface $entityManager,Proposition $proposition)
    {
        $author = $this->getUser();

        if(!$author) {
            return $this->json(['code'=>403,'message'=>'Vous n\'êtes pas autoriser a liker'],403);
        }

        if($proposition->isLikeUser($author)) {
            $postLike = $postLikeRepository->findOneBy(['author'=> $author,'proposition'=>$proposition]);
            $entityManager->remove($postLike);
            $entityManager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'like supprimé',
                'postLikes' => $postLikeRepository->count(['proposition'=>$proposition])
            ],200);
        }

        $postLike = New PostLike();
        $postLike->setAuthor($author);
        $postLike->setProposition($proposition);
        $entityManager->persist($postLike);
        $entityManager->flush();

        return $this->json([
            'code' => 200,
            'message' => 'like ajouté',
            'postLikes' => $postLikeRepository->count(['proposition'=> $proposition])
        ],200);
    }

    /**
     * @Route("/upload", name="upload", methods={"POST"})
     *
     * @param Request $request
     *
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse|FormInterface
     */
/*    public function uploadAction(Request $request,EntityManagerInterface $entityManager)
    {
        $avatar = New Image();
        $form   = $this->createForm(UploadType::class,$avatar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($avatar);
            $entityManager->flush();


        return new JsonResponse([], 201);
    }

        return $form;
    }*/

}

