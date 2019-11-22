<?php

namespace App\Controller;

use App\Repository\OrdersRepository;
use App\Repository\OrderDetailRepository;
use App\Entity\Orders;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/orders/{slug}", name="admin_orders_index")
    */
    public function orders($slug, OrdersRepository $ordersRepo) {
        $orders = $ordersRepo->findBy(['status' => $slug]);
        return $this->render('admin/orders/index.html.twig', [
            'orders' => $orders
        ]);
    }

     /**
     * @Route("/admin/orders/show/{id}", name="admin_orders_show", methods="GET")
     */
    public function orderShow($id,Orders $order, OrderDetailRepository $ordersRepository): Response
    {

        $orderdetail = $ordersRepository->findBy(
            ['orderid' =>$id]
        );


        return $this->render('admin/orders/show.html.twig', [
            'order' => $order, 
            'orderdetails' =>$orderdetail,
        ]);
    }

    /**
     * @Route("/admin/orders/update/{id}", name="admin_orders_update", methods="POST")
     */
    public function orderUpdate($id,Request $request, Orders $order): Response
    {

        $shipinfo = $request->get('shipinfo');
        $note = $request->get('note');
        $status = $request -> get('status');

        $em = $this->getDoctrine()->getManager();
        $sql = "UPDATE orders SET shipinfo=:shipinfo, note=:note, status=:status WHERE id =:id";
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('shipinfo', $shipinfo);
        $statement->bindValue('note', $note);
        $statement->bindValue('status', $status);
        $statement->bindValue('id', $id);
        $statement->execute();

        return $this->redirectToRoute('admin_orders_show', array('id' => $id));
    }


}
