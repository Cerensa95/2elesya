<?php

namespace App\Controller;

use App\Entity\Games;
use App\Entity\Sales;
use App\Repository\CategoryRepository;
use App\Repository\SettingRepository;



use App\Form\GamesType;
use App\Form\SalesType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    
    /**
     * @Route("/admin/sales", name="sales")
     */
    public function sales()
    {   
        $sales = $this -> getDoctrine()->getRepository(Sales::class)->findAll();  
        return $this->render('admin/game/games.html.twig', [
            'sales' => $sales,
        ]);
    }

    /**
     * @Route("/admin/Ürünler/add", name="add-game", methods="GET|POST")
     */
    public function addGame(Request $request, CategoryRepository $catRepo): Response
    {   
       $game = new Games();
       $form = $this->createForm(GamesType::class, $game);
       $form->handleRequest($request);

       $catList = $catRepo->findAll();



        //Save to DATABASE
        if($form->isSubmitted() && $form->isValid()) {
          
            $file = $request->files->get('imagename');
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
         
            $file->move($this->getParameter('images_directory'), $fileName);
            $game->setImage($fileName); 

            $em = $this->getDoctrine()->getManager();
            $em->persist($game);
            $em->flush();
            return $this->redirectToRoute('Ürünler');
        }

        return $this->render('admin/game/oyun-ekle.html.twig', [
            'form' => $form->createView(),
            'catList' => $catList,
        ]);
    }  

    /**
     * @Route("/admin/sales/edit/{id}", name="edit-sale", methods="GET|POST")
     */
    public function editSale(Request $request, Sales $sales,CategoryRepository $catRepo): Response
    {    

        $catList = $catRepo->findAll();

        $form = $this->createForm(SalesType::class, $sales);
        $form->handleRequest($request);

         //Save to DATABASE
        if($form->isSubmitted()) {
            $this ->getDoctrine() ->getManager()->flush();
            return $this->redirectToRoute('sales');
        }

        return $this->render('admin/game/edit-game.html.twig', [
            'sale'=>$sales,
            'catList' => $catList,
        ]);
    }

    /**
     * @Route("/admin/sales/edit/{id}/{status}", name="update-status", methods="GET|POST")
     */
    public function updateStatus(Request $request, $id, $status, Sales $sales): Response
    {   
        
        $em = $this->getDoctrine()->getManager();
        $sale = $em->getRepository(Sales::class)->find($id);
        
        if($status == "true") {
            $sale->setStatus('true');
            $em->flush();
        }else {
            $sale->setStatus('false');
            $em->flush();
        }

        return $this->redirectToRoute('sales');
    }


    /**
     * @Route("/admin/sales/delete/{id}", name="delete-sale", methods="GET|POST")
     */
    public function deleteSale(Sales $sales)
    {  
        $em = $this->getDoctrine()->getManager();
        $em->remove($sales);
        $em->flush();
        return $this->redirectToRoute('sales');
    }

    /**
     * @Route("/sell-product", name="sell-product", methods="GET|POST")
     */
    public function addSelingGame(Request $request, CategoryRepository $catRepo): Response
    { 
        $catList = $catRepo->findAll();  
        $sale = new Sales();
        $form = $this->createForm(SalesType::class, $sale);
        $form->handleRequest($request);
       
        $sale->setStatus("false");

        //Save to DATABASE
        if($form->isSubmitted()) {

            $file = $request->files->get('imagename');        
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move($this->getParameter('images_directory'), $fileName);

            $sale->setImage($fileName); 

            $em = $this->getDoctrine()->getManager();
            $em->persist($sale);
            $em->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('sell-product.html.twig', [
            'form' => $form->createView(),
            'catList' => $catList,
        ]);
    }
}
