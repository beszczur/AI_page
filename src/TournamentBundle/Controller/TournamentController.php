<?php

namespace TournamentBundle\Controller;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class TournamentController extends Controller
{
    /**
     * @Route("/show/{id}", name="show_tournament")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showTournamentAction($id)
    {
        $repository = $this->getDoctrine()
            ->getRepository('TournamentBundle:Tournament');
        $tournament = $repository->find($id);
        return $this->render('TournamentBundle:Tournament:show_tournament.html.twig', array(
            'tournament' => $tournament,
        ));
    }

    /**
     * @Route("/edit/{id}",name="edit_tournament")
     * @Method({"GET", "POST"})
     */
    public function editTournamentAction($id, Request $request)
    {
        $repository = $this->getDoctrine()
            ->getRepository('TournamentBundle:Tournament');
        $tournament = $repository->find($id);

        $form = $this->createFormBuilder($tournament)
            ->add('name', TextType::class)
            ->add('registrationEndDate', DateType::class)
            ->add('tournamentDate', DateType::class)
            ->add('city', TextType::class)
            ->add('street', TextType::class)
            ->add('description', TextareaType::class)
            ->add('participantsLimit', IntegerType::class)
            ->add('discipline', EntityType::class, array(
                'class' => 'TournamentBundle:Discipline',
                'choice_label' => 'name',
            ))
            ->add('files', FileType::class, array(
                "attr"          => array("accept" => "image/*"),
                "data_class"    => null,
                "multiple"      => "multiple",
                'required'      => false
            ))
            ->add('create', SubmitType::class, array('label' => 'Create tournament'))
            ->getForm();

            $form->handleRequest($request);
            if ($form->isValid()) {

                $tournament = $form->getData();

                $em = $this->getDoctrine()->getManager();
                $em->persist($tournament);
                $em->flush();

                return $this->redirect($this->generateUrl('show_tournament', array(
                    'id' => $tournament->getId()
                )
                ));

        }


        return $this->render('TournamentBundle:Tournament:edit_tournament.html.twig', array(
            'edit_form' => $form->createView(),
        ));
    }

    /**
     * @Route("/list", name="list_tournament")
     */
    public function listTournamentAction()
    {
        $repository = $this->getDoctrine()
            ->getRepository('TournamentBundle:Tournament');
        $tournaments = $repository->findAll();
        return $this->render('TournamentBundle:Tournament:list_tournament.html.twig', array(
            "tournaments" => $tournaments,
        ));
    }

}
