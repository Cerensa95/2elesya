<?php

namespace App\Controller;

use App\Entity\ShopCart;
use App\Form\ShopCartType;
use App\Repository\ShopCartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/shopcart")
 */
class ShopCartController extends Controller
{
    /**
     * @Route("/", name="shop_cart_index", methods="GET")
     */
    public function index(ShopCartRepository $shopCartRepository): Response
    {   
        $em = $this->getDoctrine()->getManager();
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // login controll
        $user = $this->getUser();
        $id = $user->getid();

        $sql = "SELECT p.oyun_id, p.price, s.* FROM shop_cart s, sales p
                WHERE s.productid = p.id AND userid = :userid";

        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('userid',$id);
        $statement->execute();
        $result = $statement->fetchAll();



        return $this->render('shop_cart/index.html.twig', ['shop_carts' => $result]);
    }

    /**
     * @Route("/new", name="shop_cart_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $shopCart = new ShopCart();
        $form = $this->createForm(ShopCartType::class, $shopCart);
        $form->handleRequest($request);
        
        $user = $this->getUser();
        $shopCart->setProductid($request->request->get('productid'));
        $shopCart->setName($request->request->get('name'));
        $shopCart->setUserid($user->getid());



        $em = $this->getDoctrine()->getManager();
        $em->persist($shopCart);
        $em->flush();

        return $this->redirectToRoute('shop_cart_index');
    }

    /**
     * @Route("/{id}", name="shop_cart_show", methods="GET")
     */
    public function show(ShopCart $shopCart): Response
    {
        return $this->render('shop_cart/show.html.twig', ['shop_cart' => $shopCart]);
    }

    /**
     * @Route("/{id}/edit", name="shop_cart_edit", methods="GET|POST")
     */
    public function edit(Request $request, ShopCart $shopCart): Response
    {
        $form = $this->createForm(ShopCartType::class, $shopCart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('shop_cart_edit', ['id' => $shopCart->getId()]);
        }

        return $this->render('shop_cart/edit.html.twig', [
            'shop_cart' => $shopCart,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="shop_cart_del", methods="GET|POST")
     */
    public function del(Request $request, ShopCart $shopCart): Response
    {
            $em = $this->getDoctrine()->getManager();
            $em->remove($shopCart);
            $em->flush();
            return $this->redirectToRoute('shop_cart_index');
    }

    /**
     * @Route("/{id}", name="shop_cart_delete", methods="DELETE")
     */
    public function delete(Request $request, ShopCart $shopCart): Response
    {
        if ($this->isCsrfTokenValid('delete'.$shopCart->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($shopCart);
            $em->flush();
            return $this->redirectToRoute('shop_cart_index');

        }
    }
}
