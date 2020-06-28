<?php

namespace App\Controller;

use App\Repository\PropositionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/one-up")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @param PropositionRepository $propositionRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PropositionRepository $propositionRepository, PaginatorInterface $paginator, Request $request)
    {
        $date = New \DateTime();
        $propositions = $propositionRepository->findByRecentProposition($date);
        $pagination = $paginator->paginate(
            $propositions,
            $request->query->getInt('page',1), 3
        );

        return $this->render('default/index.html.twig', [
            'propositions' => $pagination,
        ]);
    }
}
