<?php

namespace RegisterBundle\Controller;

use RegisterBundle\Entity\Token;
use RegisterBundle\Entity\User;
use RegisterBundle\Services\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'RegisterBundle:Security:login.html.twig',
            array(
                'last_username' => $lastUsername,
                'error'         => $error,
            )
        );
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction()
    {
    }

    /**
     * @Route("/forgotPassword/email/send", name="forgot_password_email_send")
     */
    public function resetPasswordEmailSendAction()
    {
        return $this->render('RegisterBundle:Security:reset_password.html.twig');
    }

    /**
     * @Route("/forgotPassword", name="forgotPassword")
     * @Method({"GET", "POST"})
     */
    public function forgotPasswordAction(Request $request)
    {
        $form = $this->createFormBuilder(new User())
            ->add('email', EmailType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {

            $user = $form->getData();

            $userRepository = $this->getDoctrine()->getRepository('RegisterBundle:User');
            $user = $userRepository->findOneBy([
                'email' => $user->getEmail(),
            ]);

            if($user == null)
            {
                return $this->render(
                    'RegisterBundle:Security:not_existing_user.html.twig'
                );
            } else {
                $token = new Token();
                $token->setUser($user);
                $tokenGenerator = new TokenGenerator();
                $token->setToken($tokenGenerator->generate());
                $token->setCreatedAt(new \DateTime());

                $emTokens = $this->getDoctrine()->getManager();
                $emTokens->persist($token);
                $emTokens->flush();

                //sending email
                $message = \Swift_Message::newInstance()
                    ->setSubject('Resetowanie hasła.')
                    ->setFrom('dearbeata@gmail.com')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'Emails/reset_password.html.twig', [
                                'name' => $user->getFirstname(),
                                'token' =>$token->getToken(),
                            ]
                        ),
                        'text/html'
                    )
                ;
                $this->get('mailer')->send($message);

                return $this->redirect($this->generateUrl('forgot_password_email_send'));
            }
        }
        return $this->render(
            'RegisterBundle:Security:forgot_password.html.twig', [
                "form" => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/resetPassword/{token}")
     */
    public function resetPasswordAction($token)
    {
        return $this->render('RegisterBundle:Security:reset_password.html.twig', array(
        ));
    }

}
