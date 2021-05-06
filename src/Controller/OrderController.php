<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderProductRepository;
use App\Repository\OrderRepository;
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
    private OrderRepository $orderRepository;
    private OrderProductRepository $orderProductRepository;

    public function __construct(ProductRepository $productRepository,
                                SessionInterface $sessionInterface,
                                EntityManagerInterface $entityManager,
                                OrderService $orderService,
                                ProductService $productService,
                                OrderRepository $orderRepository,
                                OrderProductRepository $orderProductRepository) {
        $this->productRepository = $productRepository;
        $this->sessionInterface = $sessionInterface;
        $this->entityManager = $entityManager;
        $this->orderService = $orderService;
        $this->productService = $productService;
        $this->orderRepository = $orderRepository;
        $this->orderProductRepository = $orderProductRepository;
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

            return $this->redirect($this->generateUrl('order.view', [
                'id' => $order->getId(),
                'order' => $order
            ]));
        }

        return $this->render('order/create.html.twig', [
            'items' => $cart,
            'form' => $form->createView()
        ]);
    }

    #[Route('/view/{id}', name: 'view')]
    public function show($id)
    {
        $user = $this->getUser();
        $order = $this->orderRepository->find($id);
        $orderProducts = $this->orderProductRepository->findBy(['order' => $order->getId()]);

        if ($user !== $order->getCustomer()){
            return null; // Возвращаем некую ошибку?
        }

        return $this->render('order/show.html.twig', [
            'order_products' => $orderProducts
        ]);
    }
}
