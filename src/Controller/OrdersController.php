<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\OrderDetail;
use App\Form\OrdersType;
use App\Repository\OrdersRepository;
use App\Repository\OrderDetailRepository;
use App\Repository\ShopCartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/orders")
 */
class OrdersController extends Controller
{
    /**
     * @Route("/", name="orders_index", methods="GET")
     */
    public function index(OrdersRepository $ordersRepository): Response
    {

        $user = $this->getUser();
        $userid = $user->getid();

        
        return $this->render('orders/index.html.twig', ['orders' => $ordersRepository->findBy(['userid'=>$userid])]);
    }

    /**
     * @Route("/new", name="orders_new", methods="GET|POST")
     */
    public function new(Request $request, ShopCartRepository $shopcartRepo): Response
    {
        $order = new Orders();
        $form = $this->createForm(OrdersType::class, $order);
        $form->handleRequest($request);

        $user = $this->getUser();
        $userid = $user->getid();
        $total = $shopcartRepo->getUserShopCartTotal($userid); 

        $submittedToken = $request->request->get('token');

        if($this->isCsrfTokenValid('form-order', $submittedToken)) {
            if ($form->isSubmitted()) {
                $em = $this->getDoctrine()->getManager();


                $order->setUserid($userid);
                $order->setAmount($total);
                $order->setStatus('New');

                $em->persist($order);
                $em->flush();

                $orderid = $order->getId();
                $shopcart = $shopcartRepo->getUserShopCart($userid);


                foreach ($shopcart as $item) {
                    $orderdetail = new OrderDetail();

                    $orderdetail -> setOrderid($orderid);
                    $orderdetail -> setUserid($user->getid());
                    $orderdetail -> setProductid($user->getid());
                    $orderdetail -> setPrice(232);
                    $orderdetail -> setQuantity($item["total"]);
                    $orderdetail -> setAmount($item["total"]);
                    $orderdetail -> setName($item["name"]);
                    $orderdetail -> setStatus("Ordered");


                    $em->persist($orderdetail);
                    $em->flush();

                }


                // Delete user shopcart products
                $em = $this->getDoctrine() -> getManager();
                $query = $em -> createQuery(
                    'DELETE FROM App\Entity\ShopCart s WHERE s.userid= :userid'
                )->setParameter('userid', $userid);
                $query->execute();
    
                return $this->redirectToRoute('orders_index');
            }
        }

        return $this->render('orders/new.html.twig', [
            'order' => $order,
            'total' => $total,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="orders_show", methods="GET")
     */
    public function show(Orders $order, OrderDetailRepository $ordersRepository): Response
    {
        $user = $this->getUser();
        $userid = $user->getid();
        $orderid = $order->getid();

        $orderdetail = $ordersRepository->findBy(
            ['orderid' =>$orderid]
        );


        return $this->render('orders/show.html.twig', [
            'order' => $order, 
            'orderdetails' =>$orderdetail,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="orders_edit", methods="GET|POST")
     */
    public function edit(Request $request, Orders $order): Response
    {
        $form = $this->createForm(OrdersType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('orders_edit', ['id' => $order->getId()]);
        }

        return $this->render('orders/edit.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="orders_delete", methods="DELETE")
     */
    public function delete(Request $request, Orders $order): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($order);
            $em->flush();
        }

        return $this->redirectToRoute('orders_index');
    }
}
