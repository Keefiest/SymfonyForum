<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CategorieController extends AbstractController
{
    /**
     * @Route("/categorie/add", name="add_categorie")
     * @Route("/categorie/edit/{id}", name="edit_categorie")
     * @ParamConverter("categorie", options = {"mapping": {"id": "id"}})
     */    
    public function add(ManagerRegistry $doctrine, Categorie $categorie = null, Request $request): Response
    {
        if(!$categorie){
            $categorie = new Categorie();
        }

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $categorie = $form->getData();
            $entityManager = $doctrine->getManager();
            // prepare
            $entityManager->persist($categorie);
            // insert into (execute)
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie');

        }

        // vue pour afficher le formulaire d'ajout
        return $this->render('categorie/add.html.twig', [
            'formAddCategorie' => $form->createView(),
            'edit' => $categorie->getId()
        ]);

    }
    /**
     * @Route("/categorie/delete/{id}", name="delete_categorie")
     */
    public function delete(ManagerRegistry $doctrine, Categorie $categorie){

        $entityManager = $doctrine->getManager();
        $entityManager->remove($categorie);
        $entityManager->flush();
        return $this->redirectToRoute('app_categorie');
    }

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
