<?php

namespace RegisterBundle\Controller;

use RegisterBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    /**
     * @Route("/register")
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
            ->add('password', TextType::class)
            ->add('register', SubmitType::class, array('label' => 'Register'))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {

            $user = $form->getData();
            //$user->setPassword(md5($user->getPassword()));
            $user->setOptions(0);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

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


}
