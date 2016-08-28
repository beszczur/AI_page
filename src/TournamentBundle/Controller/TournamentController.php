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

        return $this->render('TournamentBundle:Tournament:show_tournament.html.twig', [
            'tournament'     => $tournament,
            'participations' => $participations,
            'user_id'        => $this->getUserId(),
        ]);
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
            'id'        => $tournament->getId(),
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
            "user_id"     => $this->getUserId(),
        ));
    }

    /**
     * @Route("/mytournaments", name="my_tournaments")
     */
    public function myTournamentsAction()
    {
        $tournamentRepository = $this->getDoctrine()
            ->getRepository('TournamentBundle:Tournament');

        $tournaments = $tournamentRepository->findAll();
        $my_tournaments = [];
        foreach ($tournaments as $tournament)
        {
            if($this->isOrganizer($tournament) || $this->isParticipant($tournament))
            {
                $my_tournaments [] = $tournament;
            }
        }

        return $this->render('TournamentBundle:Tournament:my_tournaments.html.twig', array(
            "tournaments" => $my_tournaments,
            "user_id"     => $this->getUserId(),
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

    private function getUserId()
    {
        if($this->getUser() == null)
        {
           return 0;
        }
        else
        {
            return $this->getUser()->getId();
        }
    }

    /**
     * @param $tournament
     * @return bool
     */
    private function isOrganizer($tournament){
        return $tournament->getOrganizer()->getId() == $this->getUser()->getId();
    }

    /**
     * @param $tournament
     * @return bool
     */
    private function isParticipant($tournament){

        $participationRepository = $this->getDoctrine()
            ->getRepository('TournamentBundle:Participation');

        $paticipations = $participationRepository->findBy([
            'user'       => $this->getUser()->getId(),
            'tournament' => $tournament->getId()
        ]);

        return $paticipations != null;
    }
}


