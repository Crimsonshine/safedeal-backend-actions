<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            ->getForm()
        ;
        $form->handleRequest($request);

        $cardAdd = $session->get('cart_add', []);
        $cardAddData = [];

        foreach($cardAdd as $id => $quantity) {
            $data = $repository->find($id);

            $cardAddData[] = [
                'product' => $data,
                'quantity' => $quantity
            ];

            if ($form->isSubmitted()) {

                $order = new Order();
                $order->setCustomer($data['product.name']);
                $order->setSender($data['product.sender']);

                $em = $this->getDoctrine()->getManager();

                $em->persist($product);
                $em->flush();

                return $this->redirect($this->generateUrl('main'));
            }
        }

        //dd($cardAddData);

        return $this->render('order/create.html.twig', [
            'items' => $cardAddData,
            'form' => $form->createView()
        ]);
    }
}
