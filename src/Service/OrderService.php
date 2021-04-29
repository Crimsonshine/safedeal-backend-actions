<?php


namespace App\Service;


use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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

    public function createOrder(UserInterface $user, string $addressTo, string $creationDate, string $status): Order
    {
        $order = new Order();
        $order->setCustomer($user);
        $order->setAddressTo($addressTo);
        $order->setCreationDate($creationDate);
        $order->setStatus($status);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }
}