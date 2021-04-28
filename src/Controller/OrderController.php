<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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

    public function __construct(ProductRepository $productRepository, SessionInterface $sessionInterface, EntityManagerInterface $entityManager) {
        $this->productRepository = $productRepository;
        $this->sessionInterface = $sessionInterface;
        $this->entityManager = $entityManager;
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('address_to', TextType::class, [
                'label' =>'Введите адрес доставки',
                'attr' => [
                    'class' => 'address_to_label'
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Добавить',
                'attr' =>[
                    'class' =>'btn btn-success float-right'
                ]
            ])
            ->getForm()
        ;
        $form->handleRequest($request);

        $cardProducts = $this->sessionInterface->get('cart_add', []);
        $cardAddData = [];

        $order          = null;
        $orderProduct   = null;

        if ($form->isSubmitted()) {
            $order = new Order();
            $user = $this->getUser();
            $data = $form->getData();

            $order->setCustomer($user);
            $order->setAddressTo($data['address_to']);
            $order->setCreationDate(date('H:i:s \O\n d/m/Y'));
            $order->setStatus("оплачен");

            $this->entityManager->persist($order);
        }

        foreach($cardProducts as $cardProduct => $quantity) {
            $productData = $this->productRepository->find($cardProduct);

            $cardAddData[] = [
                'product' => $productData,
                'quantity' => $quantity
            ];

            if ($form->isSubmitted()) {
                //$product = new Product();
                $orderProduct = new OrderProduct();
                $orderProduct->setProduct($productData->getProduct());
                $orderProduct->setOrder($order);
                $orderProduct->setQuantity($quantity);

                $this->entityManager->persist($orderProduct);
                $this->entityManager->flush();
            }
        }

        if ($form->isSubmitted()) {
            $cardAdd = $this->sessionInterface->remove('cart_add');
            return $this->redirect($this->generateUrl('main'));
        }
        //dd($cardAddData);
        return $this->render('order/create.html.twig', [
            'items' => $cardAddData,
            'form' => $form->createView()
        ]);
    }
}
