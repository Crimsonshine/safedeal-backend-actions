<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product', name: 'product.')]
class ProductController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/add', name: 'add')]
    public function add(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, [
                'label' =>'Название'
            ])
            ->add('price',  MoneyType::class, [
                'label' =>'Цена',
                'currency' => 'RUB'
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

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $user = $this->getUser();

            $product = new Product();
            $product->setName($data['name']);
            $product->setPrice($data['price']);
            $product->setSender($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirect($this->generateUrl('main'));
        }

        return $this->render('product_list/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param ProductRepository $repository
     * @return Response
     */
    #[Route('/list', name: 'list')]
    public function list(ProductRepository $repository): Response
    {
        $user = $this->getUser();
        $products = $repository->findBy(['sender' => $user]);

        return $this->render('product_list/list.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/all', name: 'all')]
    public function all(ProductRepository $repository): Response
    {
        $user = $this->getUser();
        $products = $repository->findAll();

        return $this->render('product_list/all.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @param Product $product
     * @return Response
     */
    #[Route('/show/{id}', name: 'show')]
    public function show(Product $product): Response {
        return $this->render('product_list/show.html.twig', [
            'product' => $product
        ]);
    }
}
