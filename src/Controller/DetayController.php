<?php

namespace App\Controller;


use App\Entity\Sales;
use App\Entity\Image;
use App\Entity\Comments;
use App\Entity\User;
use App\Entity\UserImage;



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
        $images = $this->getDoctrine()->getRepository(UserImage::class)->findBy(["product_id" => $id]);
        $comments = $this -> getDoctrine()->getRepository(Comments::class)->findBy(array('status' => "true", 'userid' => $product->getUserId()));        
        $user = $this->getDoctrine()->getRepository(User::class)->find($product->getUserId());

        return $this->render('urun-detay.html.twig', [
            'product'=>$product,
            'images' => $images,
            'comments' => $comments,
            'user' => $user
        ]);
    }
}
