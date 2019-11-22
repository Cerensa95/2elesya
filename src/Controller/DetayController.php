<?php

namespace App\Controller;

use App\Entity\Games;
use App\Entity\Sales;
use App\Entity\Image;
use App\Entity\Sliders;
use App\Entity\Comments;
use App\Entity\ShopCart;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DetayController extends Controller
{
    /**
     * @Route("/oyun-detay/{id}", name="game-detay")
     */
    public function index($id)

    {
        
        $images = $this->getDoctrine()->getRepository(Image::class)->findBy(["product_id" => $id]);
        $game = $this->getDoctrine()->getRepository(Games::class)->find($id);
        $sales = $this -> getDoctrine()->getRepository(Sales::class)->findBy(array("oyunId"=> $id), array('price'=>'ASC'));
        $comments = $this -> getDoctrine()->getRepository(Comments::class)->findBy(array('status' => "true", 'oyun_id' => $id));
        $minPrice = $sales[0];
        
        return $this->render('game-detay.html.twig', [
            'game' => $game,
            'sales' => $sales,
            'images' => $images,
            'comments' => $comments,
            'minPrice' => $minPrice,
        ]);
    }
}
