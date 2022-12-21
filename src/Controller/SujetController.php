<?php

namespace App\Controller;

use DateTime;
use App\Entity\Sujet;
use App\Form\SujetType;
use App\Entity\Categorie;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class SujetController extends AbstractController
{
    /**
     * @Route("/sujet/add/{idCategorie}", name="add_sujet")
     * @ParamConverter("categorie", options = {"mapping": {"idCategorie": "id"}})
     */    
    public function add(ManagerRegistry $doctrine, Sujet $sujet = null, Request $request, Categorie $categorie, Security $security): Response
    {
        if(!$sujet){
            $sujet = new Sujet();
        }

        $form = $this->createForm(SujetType::class, $sujet);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // on récup l'utilisateur en session et on l'ajoute au sujet
            $user = $security->getUser();
            $sujet->setUSer($user);
            // on défini une variable DateTime actuel, et on l'ajoute au sujet
            $dateTopic = new DateTime;
            $sujet->setDateCreation($dateTopic);
            // on set la catégorie dans la route
            $sujet->setCategorie($categorie);
        
            $sujet = $form->getData();
            $entityManager = $doctrine->getManager();
            // prepare
            $entityManager->persist($sujet);
            // insert into (execute)
            $entityManager->flush();

            return $this->redirectToRoute('app_sujet', ['id' => $categorie->getId()]);

        }

        // vue pour afficher le formulaire d'ajout
        return $this->render('sujet/add.html.twig', [
            'formAddSujet' => $form->createView(),
        ]);

    }
    /**
     * @Route("/sujet/edit/{idSujet}", name="edit_sujet")
     * @ParamConverter("sujet", options = {"mapping": {"idSujet": "id"}})
     */    
    public function edit(ManagerRegistry $doctrine, Sujet $sujet = null, Request $request, Security $security): Response
    {
        if(!$sujet){
            $sujet = new Sujet();
        }

        $form = $this->createForm(SujetType::class, $sujet);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // on récup l'utilisateur en session et on l'ajoute au sujet
            $user = $security->getUser();
            $sujet->setUSer($user);

            $sujet = $form->getData();
            $entityManager = $doctrine->getManager();
            // prepare
            $entityManager->persist($sujet);
            // insert into (execute)
            $entityManager->flush();

            return $this->redirectToRoute('app_sujet', ['id' => $sujet->getCategorie()->getId()]);

        }

        // vue pour afficher le formulaire d'ajout
        return $this->render('sujet/add.html.twig', [
            'formAddSujet' => $form->createView(),
            'edit' => $sujet->getId()
        ]);

    }

    /**
     * @Route("/sujet/{id}/delete", name="delete_sujet")
     */
    public function delete(ManagerRegistry $doctrine, Sujet $sujet){

        $entityManager = $doctrine->getManager();
        $entityManager->remove($stagiaire);
        $entityManager->flush();
        return $this->redirectToRoute('app_sujet', ["id" => $sujet->getCategorie()->getSujet()]);
    }

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
