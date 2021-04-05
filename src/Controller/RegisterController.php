<?php

namespace App\Controller;

use App\Entity\User;
use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
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
            ->add('fullname', TelType::class, [
                'label' =>'Full name'
            ])
            ->add('telnumber', TelType::class,  [
                'label' =>'Telephone'
            ])
            ->add('birthday', DateType::class, [
                'years' => range(1940,2021)
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirm Password']
            ])
            ->add('register', SubmitType::class, [
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
            $user->setFullname($data['fullname']);
            $user->setTelnumber($data['telnumber']);
            $user->setBirthday($data['birthday']);
            $user->setPassword(
                $passwordEncoder->encodePassword($user,$data['password'])
            );
            $user->setRegisterdate(date('H:i:s \O\n d/m/Y'));
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
