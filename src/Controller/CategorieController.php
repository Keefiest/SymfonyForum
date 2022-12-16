<?php

namespace App\Controller;

use App\Entity\Categorie;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategorieController extends AbstractController
{
    /**
     * @Route("/categorie", name="app_categorie")
     */
    public function index(ManagerRegistry $doctrine, PaginatorInterface $paginator, Request $request): Response
    {
        $categories = $doctrine->getRepository(Categorie::class)->findBy([], ['nomCategorie' => 'ASC']);
        $categories = $paginator->paginate(
            $categories, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );
        return $this->render('categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }
}
