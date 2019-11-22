<?php

namespace App\Controller;


use App\Entity\Sales;
use App\Entity\Image;
use App\Entity\Comments;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DetayController extends Controller
{
    /**
     * @Route("/urun-detay/{id}", name="urun-detay")
     */
    public function index($id)

    {
        $product=$this->getDoctrine()->getRepository(Sales::class)->find($id);
        $images = $this->getDoctrine()->getRepository(Image::class)->findBy(["product_id" => $id]);
        $comments = $this -> getDoctrine()->getRepository(Comments::class)->findBy(array('status' => "true", 'oyun_id' => $id));
        
        
        return $this->render('urun-detay.html.twig', [
            'product'=>$product,
            'images' => $images,
            'comments' => $comments,
        ]);
    }
}
