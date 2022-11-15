<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Repository\OrderRepository;
use App\Repository\OrderDetailRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    #[Route('/order/details/{id}', name: 'order_details')]
    public function index(int $id, OrderDetail $orderDetail, OrderDetailRepository $orderDetailRepository): Response
    {
        $orderDetail = $orderDetailRepository->findBy(['orders' => $id]);
        return $this->render('order/orderDetails.html.twig', [
            'orders_details' => $orderDetail,
        ]);
    }

    #[Route('/order/details/delete/{id}', name: 'order_delete')]
    public function delete(Order $order, ManagerRegistry $managerRegistry): Response
    {
        $manager = $managerRegistry->getManager();
        $manager->remove($order); // supprime la commande
        $manager->flush();

        $this->addFlash('success', 'La commande a été supprimée avec succès.'); // msg de succès
        return $this->redirectToRoute('admin_orders');
    }

    #[Route('/admin/orders', name: 'admin_orders')]
    public function orderListAdmin(OrderRepository $orderRepository, OrderDetailRepository $orderDetailRepository): Response
    {
        $orders = $orderRepository->findAll();
        return $this->render('order/orderListAdmin.html.twig', [
            'orders' => $orders,
            'order_details' => $orderDetailRepository->findAll()
        ]);
    }
}