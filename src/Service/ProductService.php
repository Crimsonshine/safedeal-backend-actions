<?php

namespace App\Service;

use App\Entity\Product;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;

class ProductService
{
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
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

    public function createProduct(array $data, UserInterface $user): Product
    {
        $product = new Product();
        $product->setName($data['name']);
        $product->setPrice($data['price']);
        $product->setAddressFrom($data['address_from']);
        $product->setSender($user);

        return $product;
    }

    public function uploadImage(Product $product, Request $request)
    {
        $file = $request->files->get('form')['attachment'];
        /** @var UploadedFile $file */
        if ($file) {
            $filename = md5(uniqid()) . '.' . $file->guessClientExtension();
            $file->move(
                $this->params->get('uploads_dir'), $filename
            );
            $product->setImage($filename);
        }
    }
}