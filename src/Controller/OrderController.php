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
use Symfony\Component\Validator\Constraints\All;

class OrderController extends AbstractController
{
    // ---------------------------- ADMIN : Détail d'une commande ----------------------------
    #[Route('/order/details/{id}', name: 'order_details')]
    public function index(int $id, OrderDetailRepository $orderDetailRepository): Response
    {
        return $this->render('order/orderDetails.html.twig', [
            'orders_details' => $orderDetailRepository->findBy(['orders' => $id])
        ]);
    }

    // ---------------------------- ADMIN : Supprimer le détail d'une commande ----------------------------
    #[Route('/order/details/delete/{id}', name: 'orderDetail_delete')]
    public function delete(OrderDetail $orderDetail, ManagerRegistry $managerRegistry): Response
    {
        $manager = $managerRegistry->getManager();
        $manager->remove($orderDetail); // supprime la commande
        $manager->flush();

        $this->addFlash('success', 'La commande a été supprimée avec succès.'); // msg de succès
        return $this->redirectToRoute('admin_orders');
    }

    // ---------------------------- ADMIN : Liste des commandes ----------------------------
    #[Route('/admin/orders', name: 'admin_orders')]
    public function orderListAdmin(OrderRepository $orderRepository, OrderDetailRepository $orderDetailRepository): Response
    {
        $orders = $orderRepository->findAll();
        return $this->render('order/orderListAdmin.html.twig', [
            'orders' => $orders,
        ]);
    }

    // ---------------------------- ADMIN : Supression commandes ----------------------------
    #[Route('/admin/orders/delete/{id}', name: 'order_delete')]
    public function deleteOrder(Order $order, ManagerRegistry $managerRegistry): Response
    {
        $manager = $managerRegistry->getManager();
        $manager->remove($order); // supprime la commande
        $manager->flush();

        $this->addFlash('success', 'La commande a été supprimée avec succès.'); // msg de succès
        return $this->redirectToRoute('admin_orders');
    }
}