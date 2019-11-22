<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Games;

use App\Form\ImageType;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/image")
 */
class ImageController extends Controller
{
    /**
     * @Route("/", name="image_index", methods="GET")
     */
    public function index(ImageRepository $imageRepository): Response
    {
        return $this->render('admin/image/index.html.twig', ['images' => $imageRepository->findAll()]);
    }

    /**
     * @Route("/new", name="image_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {


        $image = new Image();
        $games = $this -> getDoctrine()->getRepository(Games::class)->findAll();  
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        

        if ($form->isSubmitted() && $form->isValid()) {

            $produtId = $request->request->get('image')['product_id'];
            $game = $this -> getDoctrine()->getRepository(Games::class)->findBy(["id"=> $produtId]);
            $oyunName = $game[0]->getName();

            $image->setName($oyunName);

            $file = $request->files->get('imagename');
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
         
            $file->move($this->getParameter('images_directory'), $fileName);

            $image->setImage($fileName); 


            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();

            return $this->redirectToRoute('image_index');
        }

        return $this->render('admin/image/new.html.twig', [
            'image' => $image,
            'games' => $games,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="image_delete", methods="DELETE")
    */
    public function delete(Request $request, Image $image): Response
    {
        
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();
        }

        return $this->redirectToRoute('image_index');
    }

    /**
     * @Route("/{id}", name="image_show", methods="GET")
     */
    public function show(Image $image): Response
    {
        return $this->render('admin/image/show.html.twig', ['image' => $image]);
    }

    /**
     * @Route("/{id}/edit", name="image_edit", methods="GET|POST")
     */
    public function edit(Request $request, Image $image): Response
    {
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('image_edit', ['id' => $image->getId()]);
        }

        return $this->render('admin/image/edit.html.twig', [
            'image' => $image,
            'form' => $form->createView(),
        ]);
    }

  
}
