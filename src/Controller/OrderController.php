<?php

namespace App\Controller;

use App\Entity\OrderProduct;
use App\Repository\ProductRepository;
use App\Service\OrderService;
use App\Service\ProductService;
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
    private ProductService $productService;

    public function __construct(ProductRepository $productRepository, SessionInterface $sessionInterface, EntityManagerInterface $entityManager, OrderService $orderService, ProductService $productService) {
        $this->productRepository = $productRepository;
        $this->sessionInterface = $sessionInterface;
        $this->entityManager = $entityManager;
        $this->orderService = $orderService;
        $this->productService = $productService;
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request): Response
    {
        $form = $this->orderService->getForm();
        $form->handleRequest($request);

        $cardProducts   = $this->sessionInterface->get('cart_add', []);
        $cart           = $this->productService->addToCart($cardProducts);

        if ($form->isSubmitted()) {
            $user = $this->getUser();
            $data = $form->getData();

            $order = $this->orderService->createOrder($user, $data['address_to'], $cart);
            $this->sessionInterface->remove('cart_add');
            return $this->redirect($this->generateUrl('main'));
        }

        return $this->render('order/create.html.twig', [
            'items' => $cart,
            'form' => $form->createView()
        ]);
    }
}
