<?php

namespace App\Controller;

use App\Entity\OrderProduct;
use App\Repository\ProductRepository;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/order', name: 'order.')]
class OrderController extends AbstractController
{
    private ProductRepository $productRepository;
    private SessionInterface $sessionInterface;
    private EntityManagerInterface $entityManager;
    private OrderService $orderService;

    public function __construct(ProductRepository $productRepository, SessionInterface $sessionInterface, EntityManagerInterface $entityManager, OrderService $orderService) {
        $this->productRepository = $productRepository;
        $this->sessionInterface = $sessionInterface;
        $this->entityManager = $entityManager;
        $this->orderService = $orderService;
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request): Response
    {
        $form = $this->orderService->getForm();
        $form->handleRequest($request);

        $cardProducts   = $this->sessionInterface->get('cart_add', []);
        $order          = null;

        if ($form->isSubmitted()) {
            $user = $this->getUser();
            $data = $form->getData();

            $order = $this->orderService->createOrder($user, $data['address_to'], date('H:i:s \O\n d/m/Y'), "оплачен");
        }

        $cardAddData = $this->orderService->createOrderProductItems($form, $cardProducts, $order);

        if ($form->isSubmitted()) {
            $this->sessionInterface->remove('cart_add');
            return $this->redirect($this->generateUrl('main'));
        }
        //dd($cardAddData);
        return $this->render('order/create.html.twig', [
            'items' => $cardAddData,
            'form' => $form->createView()
        ]);
    }
}
