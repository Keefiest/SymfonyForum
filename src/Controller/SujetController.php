<?php

namespace App\Controller;

use App\Entity\Categorie;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SujetController extends AbstractController
{
    /**
     * @Route("/sujet/{id}", name="app_sujet")
     */
    public function index(Categorie $categorie, ManagerRegistry $doctrine, PaginatorInterface $paginator, Request $request): Response
    {
        $sujets = $categorie->getSujets();
        $sujets = $paginator->paginate(
            $sujets, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );
        return $this->render('sujet/index.html.twig', [
            'categorie' => $categorie,
            'sujets' => $sujets
        ]);
    }
}
