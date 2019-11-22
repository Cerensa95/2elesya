<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Form\MessagesType;
use App\Repository\MessagesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/messages")
 */
class MessagesController extends Controller
{
    /**
     * @Route("/", name="messages_index", methods="GET")
     */
    public function index(MessagesRepository $messagesRepository): Response
    {
        return $this->render('messages/index.html.twig', ['messages' => $messagesRepository->findAll()]);
    }

    /**
     * @Route("/new", name="messages_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $message = new Messages();
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute('messages_index');
        }

        return $this->render('messages/new.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="messages_show", methods="GET")
     */
    public function show(Messages $message, $id): Response
    {   
        
        $em = $this->getDoctrine()->getManager();
        $sql = "UPDATE messages SET status ='Okundu' WHERE id = :id";
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('id', $id);
        $statement->execute();

        return $this->render('messages/show.html.twig', ['message' => $message]);
    }

    /**
     * @Route("/{id}/edit", name="messages_edit", methods="GET|POST")
     */
    public function edit(Request $request, Messages $message): Response
    {
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('messages_edit', ['id' => $message->getId()]);
        }

        return $this->render('messages/edit.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/update", name="admin_messages_update", methods="GET|POST")
     */
    public function updateMessage(Request $request, Messages $message, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $sql = "UPDATE messages SET comment = :comment WHERE id = :id";
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('id', $id);
        $statement->bindValue('comment', $request->request->get('comment'));
        $statement->execute();
       

        return $this->render('messages/show.html.twig', ['message' => $message, 'id'=>$id]);
    }

    /**
     * @Route("/{id}", name="messages_delete", methods="DELETE")
     */
    public function delete(Request $request, Messages $message): Response
    {
        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($message);
            $em->flush();
        }

        return $this->redirectToRoute('messages_index');
    }
}
