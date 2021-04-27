<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/order', name: 'order.')]
class OrderController extends AbstractController
{
    #[Route('/create', name: 'create')]
    public function create(Request $request, SessionInterface $session, ProductRepository $repository): Response
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

        $cardAdd = $session->get('cart_add', []);
        $cardAddData = [];

        $i = 1;
        foreach($cardAdd as $id => $quantity) {
            $productData = $repository->find($id);

            $cardAddData[] = [
                'product' => $productData,
                'quantity' => $quantity
            ];

            if ($form->isSubmitted()) {
                $order = null;
                $orderProduct = null;

                if ($i == 1) {
                    $order = new Order();
                    $orderProduct = new OrderProduct();
                    $user = $this->getUser();
                    $data = $form->getData();

                    $order->setCustomer($user);
                    //$order->setCourier();
                    $order->setAddressTo($data['address_to']);
                    //$order->setDeliveryDate();
                    $order->setCreationDate(date('H:i:s \O\n d/m/Y'));
                    $order->setStatus("оплачен");
                }

                //$product = new Product();
                $orderProduct->addProduct($productData->getProduct());
                $orderProduct->setOrder($order);
                $orderProduct->setQuantity($quantity);

                $em = $this->getDoctrine()->getManager();
                $em->persist($order);
                $em->persist($orderProduct);
                $em->flush();

                return $this->redirect($this->generateUrl('main'));
            }

            $i++;
        }

        //dd($cardAddData);

        return $this->render('order/create.html.twig', [
            'items' => $cardAddData,
            'form' => $form->createView()
        ]);
    }
}
