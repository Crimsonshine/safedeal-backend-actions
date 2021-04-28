<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product', name: 'product.')]
class ProductController extends AbstractController
{
    private ProductRepository $productRepository;
    private SessionInterface $sessionInterface;
    private ProductService $productService;
    private EntityManagerInterface $entityManager;

    public function __construct(ProductRepository $productRepository, SessionInterface $sessionInterface, ProductService $productService, EntityManagerInterface $entityManager) {
        $this->productRepository = $productRepository;
        $this->sessionInterface = $sessionInterface;
        $this->productService = $productService;
        $this->entityManager = $entityManager;
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request): Response
    {
        $form = $this->productService->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $user = $this->getUser();

            $product = $this->productService->createProduct($data, $user);
            $this->productService->uploadImage($product, $request);
            $this->entityManager->persist($product);
            $this->entityManager->flush();

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
