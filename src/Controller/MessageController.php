<?php

namespace App\Controller;

use App\Entity\Sujet;
use App\Entity\Message;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{
    /**
     * @Route("/message/{id}", name="app_message")
     */
    public function index(Sujet $sujet, ManagerRegistry $doctrine, PaginatorInterface $paginator, Request $request): Response
    {
        $messages = $sujet->getMessages();
        $messages = $paginator->paginate(
            $messages, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );
        return $this->render('message/index.html.twig', [
            'sujet' => $sujet,
            'messages' => $messages
        ]); 
    }
}
