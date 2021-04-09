<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Date;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createFormBuilder()
            ->add('email')
            ->add('full_name', TextType::class, [
                'label' =>'Полное имя'
            ])
            ->add('tel_number', TelType::class,  [
                'label' =>'Телефон'
            ])
            ->add('birthday', DateType::class, [
                'label' =>'День рождения',
                'years' => range(1940,2021)
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options'  => ['label' => 'Пароль'],
                'second_options' => ['label' => 'Потвердите пароль']
            ])
            ->add('register', SubmitType::class, [
                'label' => 'Зарегестрироваться',
                'attr' =>[
                    'class' =>'btn btn-success float-right'
                ]
            ])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();

            $user = new User();
            $user->setEmail($data['email']);
            $user->setRoles(['ROLE_USER']);
            $user->setFullName($data['full_name']);
            $user->setTelNumber($data['tel_number']);
            $user->setBirthday($data['birthday']);
            $user->setPassword(
                $passwordEncoder->encodePassword($user,$data['password'])
            );
            $user->setRegisterDate(date('H:i:s \O\n d/m/Y'));
            //dump($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('app_login'));
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
