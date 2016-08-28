<?php

namespace RegisterBundle\Controller;

use RegisterBundle\Entity\Token;
use RegisterBundle\Entity\User;
use RegisterBundle\Services\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    /**
     * @Route("/register", name="register_page")
     * @Method({"GET", "POST"})
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('firstname', TextType::class)
            ->add('surname', TextType::class)
            ->add('gender', ChoiceType::class, array(
                'choices' => array('Female' => 'F', 'Male' => 'M')))
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {

            //adding user to database
            $user = $form->getData();
//            $encoder = $this->container->get('security.password_encoder');
//            $encoded = $encoder->encodePassword($user, $user->getPassword());
//            $user->setPassword($encoded);
            //$user->setPassword(md5($user->getPassword()));
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);
            $user->setOptions(0);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            //adding token to database
            $token = new Token();
            $userRepository = $this->getDoctrine()->getRepository('RegisterBundle:User');
            $token->setUser($userRepository->findOneBy(
                    array('email' => $user->getEmail())));
            $tokenGenerator = new TokenGenerator();
            $token->setToken($tokenGenerator->generate());
            $token->setCreatedAt(new \DateTime());

            $emTokens = $this->getDoctrine()->getManager();
            $emTokens->persist($token);
            $emTokens->flush();


            //sending email
            $message = \Swift_Message::newInstance()
                ->setSubject('Rejestracja przebiegła pomyślnie.')
                ->setFrom('dearbeata@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'Emails/registration.html.twig', [
                            'name' => $user->getFirstname(),
                            'token' =>$token->getToken(),
                    ]
                    ),
                    'text/html'
                )
            ;
            $this->get('mailer')->send($message);

            return $this->redirect($this->generateUrl('register_success',
                array('user-email' => $user->getEmail())));
            /*Przekierowanie użytkownika po udanym zgłoszeniu formularza uniemożliwia użytkownikowi, by odświeżył i ponownie przesłał dane.*/
        }

        return $this->render('RegisterBundle:Register:register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/register/success", name="register_success")
     */
    public function registerSuccessAction()
    {
        return $this->render('RegisterBundle:Register:register_success.html.twig', array(
        ));
    }

    /**
     * @Route("/email/verify/{token}")
     */
    public function verifyEmailAction($token)
    {
        return $this->render('RegisterBundle:Register:email_verified.html.twig', array(
        ));
    }


}
