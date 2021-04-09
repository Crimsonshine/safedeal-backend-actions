<?php

namespace App\Controller;

use App\Entity\ProductList;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductListController extends AbstractController
{
    #[Route('/product/list', name: 'product_list')]
    public function index(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('product_name', TextType::class, [
                'label' =>'Название'
            ])
            ->add('product_price',  MoneyType::class, [
                'label' =>'Цена',
                'currency' => 'RUB'
            ])
            ->add('register', SubmitType::class, [
                'label' => 'Добавить',
                'attr' =>[
                    'class' =>'btn btn-success float-right'
                ]
            ])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $user = $this->getUser();

            $product = new ProductList();
            $product->setProductName($data['product_name']);
            $product->setProductPrice($data['product_price']);
            $product->setSender($user);
            dump($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirect($this->generateUrl('app_login'));
        }

        return $this->render('product_list/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
