<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product', name: 'product.')]
class ProductController extends AbstractController
{
    private ProductRepository $productRepository;
    private SessionInterface $sessionInterface;

    public function __construct(ProductRepository $productRepository, SessionInterface $sessionInterface) {
        $this->productRepository = $productRepository;
        $this->sessionInterface = $sessionInterface;
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, [
                'label' =>'Название'
            ])
            ->add('attachment',FileType::class, [
                'label' => 'Картинка',
                'mapped' => false
            ])
            ->add('price',  MoneyType::class, [
                'label' =>'Цена',
                'currency' => 'RUB'
            ])
            ->add('address_from',  TextType::class, [
                'label' =>'Адрес отправки'
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
            $product->setAddressFrom($data['address_from']);
            $product->setSender($user);

            $em = $this->getDoctrine()->getManager();

            $file = $request->files->get('form')['attachment'];
            /** @var UploadedFile $file */
            if ($file) {
                $filename = md5(uniqid()) . '.' . $file->guessClientExtension();
                $file->move(
                    $this->getParameter('uploads_dir'), $filename
                );
                $product->setImage($filename);
            }

            $em->persist($product);
            $em->flush();

            return $this->redirect($this->generateUrl('main'));
        }

        return $this->render('product/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/list', name: 'list')]
    public function list(): Response
    {
        $user = $this->getUser();
        $products = $this->productRepository->findBy(['sender' => $user]);

        return $this->render('product/list.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/all', name: 'all')]
    public function all(): Response
    {
        $user = $this->getUser();
        $products = $this->productRepository->findAll();

        return $this->render('product/all.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/cart_add/{id}', name: 'cart_add')]
    public function cartAdd($id)
    {
        $cardAdd = $this->sessionInterface->get('cart_add', []);

        if (!empty($cardAdd[$id])){
            $cardAdd[$id]++;
        } else {
            $cardAdd[$id] = 1;
        }

        $this->sessionInterface->set('cart_add', $cardAdd);
        //dd($session->get('cart_add'));
        return $this->redirect($this->generateUrl('product.all'));
    }

    #[Route('/show/{id}', name: 'show')]
    public function show(Product $product): Response {
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }
}
