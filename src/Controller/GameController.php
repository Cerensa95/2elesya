<?php

namespace App\Controller;

use App\Entity\Games;
use App\Entity\Sales;
use App\Entity\Messages;
use App\Entity\Admin\Category;
use App\Repository\CategoryRepository;
use App\Repository\SettingRepository;



use App\Form\GamesType;
use App\Form\SalesType;
use App\Form\MessagesType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GameController extends Controller
{
    /**
     * @Route("/admin/oyunlar", name="oyunlar")
     */
    public function index()
    {   
        $games = $this -> getDoctrine()->getRepository(Games::class)->findAll();  
        return $this->render('admin/game/games.html.twig', [
            'games' => $games,
        ]);
    }

    /**
     * @Route("/admin/sales", name="sales")
     */
    public function sales()
    {   
        $sales = $this -> getDoctrine()->getRepository(Sales::class)->findAll();  
        return $this->render('admin/sales/sales.html.twig', [
            'sales' => $sales,
        ]);
    }

    /**
     * @Route("/admin/oyunlar/add", name="add-game", methods="GET|POST")
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
            return $this->redirectToRoute('oyunlar');
        }

        return $this->render('admin/game/oyun-ekle.html.twig', [
            'form' => $form->createView(),
            'catList' => $catList,
        ]);
    }  

    /**
     * @Route("/admin/oyunlar/edit/{id}", name="edit-game", methods="GET|POST")
     */
    public function editGame(Request $request, Games $games,CategoryRepository $catRepo): Response
    {    

       $catList = $catRepo->findAll();

        $form = $this->createForm(GamesType::class, $games);
        $form->handleRequest($request);
        

        

         //Save to DATABASE
        if($form->isSubmitted() && $form->isValid()) {
            
            $this ->getDoctrine() ->getManager()->flush();

            return $this->redirectToRoute('oyunlar');
        }

        return $this->render('admin/game/edit-game.html.twig', [
            'game'=>$games,
            'catList' => $catList,

        ]);
    }

    /**
     * @Route("/admin/oyunlar/edit/{id}/{status}", name="update-status", methods="GET|POST")
     */
    public function updateStatus(Request $request, $id, $status, Games $games): Response
    {   
        
        $em = $this->getDoctrine()->getManager();
        $game = $em->getRepository(Games::class)->find($id);
        
        if($status == "true") {
            $game->setStatus('true');
            $em->flush();
        }else {
            $game->setStatus('false');
            $em->flush();
        }

        return $this->redirectToRoute('oyunlar');
    }


    /**
     * @Route("/admin/oyunlar/delete/{id}", name="delete-game", methods="GET|POST")
     */
    public function deleteGame(Games $games)
    {  
        $em = $this->getDoctrine()->getManager();
        $em->remove($games);
        $em->flush();
        return $this->redirectToRoute('oyunlar');
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
       
       $games = $this -> getDoctrine()->getRepository(Games::class)->findAll();  

        //Save to DATABASE
        if($form->isSubmitted()) {
            $gameID = $request->request->get('sales')['oyunid']; 
            $game = $this -> getDoctrine()->getRepository(Games::class)->findBy(['id' => $gameID]);
            //$gameName = $game[0]->getName();
            //$sale->setName($gameName);

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

    /**
     * @Route("/contact", name="contact", methods="GET|POST")
     */
    public function contact(Request $request, SettingRepository $settingRepository): Response
    {  
        
        $data = $settingRepository->findAll();
        
        $message = new Messages();
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);
        
        $submittedToken = $request->request->get('token');

        if ($form->isSubmitted()) {

          
           if($this->isCsrfTokenValid('form-message', $submittedToken)) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($message);
                $em->flush();

                return $this->redirectToRoute('home');
           }
        }

        return $this->render('contact.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
            'data' => $data[0],
        ]);
       
    }
}
