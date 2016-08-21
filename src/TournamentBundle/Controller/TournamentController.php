<?php

namespace TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use TournamentBundle\Entity\Tournament;

class TournamentController extends Controller
{
    /**
     * @Route("/show/{id}", name="show_tournament")
     */
    public function showTournamentAction($id)
    {
        $tournamentRepository = $this->getDoctrine()
            ->getRepository('TournamentBundle:Tournament');
        $tournament = $tournamentRepository->find($id);

        $participationRepository = $this->getDoctrine()
            ->getRepository('TournamentBundle:Participation');
        $participations = $participationRepository->findBy(array('tournament' => $id));

        return $this->render('TournamentBundle:Tournament:show_tournament.html.twig', array(
            'tournament' => $tournament,
            'participations' => $participations,
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
        $form = $this->createForm('TournamentBundle\Form\Type\TournamentType', $tournament);

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
     * @Route("/", name="list_tournament")
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

    /**
     * @Route("/add",name="add_tournament")
     * @Method({"GET", "POST"})
     */
    public function addTournamentAction(Request $request)
    {
        $repository = $this->getDoctrine()
            ->getRepository('TournamentBundle:Tournament');

        $tournament = new Tournament();
        $form = $this->createForm('TournamentBundle\Form\Type\TournamentType', $tournament);

        $form->handleRequest($request);
        if ($form->isValid()) {

            $tournament = $form->getData();
            $tournament->setOrganizer($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($tournament);
            $em->flush();

            return $this->redirect($this->generateUrl('show_tournament', array(
                    'id' => $tournament->getId()
                )
            ));
        }

        return $this->render('TournamentBundle:Tournament:add_tournament.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
