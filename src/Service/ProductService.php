<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;

class ProductService
{
    private ParameterBagInterface $params;
    private EntityManagerInterface $entityManager;
    private productRepository $productRepository;

    public function __construct(ParameterBagInterface $params, EntityManagerInterface $entityManager, productRepository $productRepository)
    {
        $this->params = $params;
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
    }

    public function getForm(): FormInterface
    {
        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->getFormFactory();
        $form = $formFactory->createBuilder()
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

        return $form;
    }

    public function uploadFile(UploadedFile $file = null): string
    {
        /** @var UploadedFile $file */
        if ($file) {
            $filename = md5(uniqid()) . '.' . $file->guessClientExtension();
            $file->move(
                $this->params->get('uploads_dir'), $filename
            );

            return $filename;
        }

        return "";
    }

    public function createProduct(UserInterface $user, string $name, float $price, string $addressFrom, UploadedFile $file = null): Product
    {
        $product = new Product();
        $product->setName($name);
        $product->setPrice($price);
        $product->setAddressFrom($addressFrom);
        $product->setSender($user);
        $product->setImage($this->uploadFile($file));

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    public function addToCart(array $cartProducts): array
    {
        $cardAddData = [];

        foreach($cartProducts as $cardProduct => $quantity) {
            $productData = $this->productRepository->find($cardProduct);

            $cardAddData[] = [
                'product' => $productData,
                'quantity' => $quantity
            ];
        }

        return $cardAddData;
    }

}