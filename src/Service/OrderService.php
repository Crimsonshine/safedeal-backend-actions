<?php


namespace App\Service;


use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderService
{
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;

    public function __construct(EntityManagerInterface $entityManager, ProductRepository $productRepository)
    {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
    }

    public function getForm(): FormInterface
    {
        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->getFormFactory();
        $form = $formFactory->createBuilder()
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

        return $form;
    }

    public function createOrderProduct(Order $order, Product $productData, int $quantity): OrderProduct
    {
        $orderProduct = new OrderProduct();
        $orderProduct->setProduct($productData->getProduct());
        $orderProduct->setOrder($order);
        $orderProduct->setQuantity($quantity);

        $this->entityManager->persist($orderProduct);
        $this->entityManager->flush();

        return $orderProduct;
    }

    public function createOrder(UserInterface $user, string $addressTo, array $cart): Order
    {
        $order = new Order();
        $order->setCustomer($user);
        $order->setAddressTo($addressTo);
        $order->setCreationDate(date('H:i:s \O\n d/m/Y'));
        $order->setStatus($order::STATUS_PAID);

        foreach($cart as $cartItem){
            $this->createOrderProduct($order, $cartItem['product'], $cartItem['quantity']);
        }

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }
}