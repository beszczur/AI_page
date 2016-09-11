<?php

namespace TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TournamentBundle\Entity\Game;
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

        $gamesRepository = $this->getDoctrine()->getRepository('TournamentBundle:Game');
        $gamesResults = [];
        $count = $tournament->getPowerOfParticipants();
        $round = 1;
        while($count >= 1){
            $roundGames = [];
            for($i = 0; $i < $count; $i++)
            {
                $game = $gamesRepository->findOneBy([
                    'tournament'    => $tournament,
                    'round'         => $round,
                    'position'      => $i
                ]);
                if($game && $game->getResult1() !== null && $game->getResult2() !== null)
                    $roundGames[] = ($game->getResult1()==1 ? [1,0] : [0,1]);
                else
                    $roundGames[] = [0,0];
            }
            $gamesResults[] = $roundGames;
            $round += 1;
            $count /= 2;
        }
        $gamesResults = json_encode($gamesResults);

        return $this->render('TournamentBundle:Tournament:show_tournament.html.twig', [
            'tournament'     => $tournament,
            'participations' => $participations,
            'user_id'        => $this->getUserId(),
            'gamesResults'   => $gamesResults,
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
            'edit_form'     => $form->createView(),
            'delete_form'   => $this->createDeleteForm($tournament)->createView(),
            'id'            => $tournament->getId(),
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

    /**
     * @Route("/{id}/generate_matches", name="tournament_generate_matches")
     */
    public function generateMatchesAction(Request $request, $id)
    {
        $tournamentRepository = $this->getDoctrine()->getRepository('TournamentBundle:Tournament');
        $tournament = $tournamentRepository->find($id);

        $participations = $tournament->getParticipants();
        $count = 2;
        while($count < $tournament->countParticipants())
            $count *= 2;
        $count /= 2;
        echo $count.PHP_EOL;
        $games = [];
        for($i = 0; $i < $count; $i++)
        {
            $games[] = new Game();
            $games[$i]->setTournament($tournament);
            $games[$i]->setRound(1);
            $games[$i]->setPosition($i);
        }

        for($i = 0; $i < $tournament->countParticipants(); $i++)
        {
            if($i < $count)
                $games[$i]->setPlayer1($participations[$i]->getUser());
            else
                $games[$i%$count]->setPlayer2($participations[$i]->getUser());
        }
        $em = $this->getDoctrine()->getEntityManager();
        for($i = 0; $i < $count; $i++){
            $em->persist($games[$i]);
        }
        $em->flush();
        return new Response('OK');
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

    /**
     * Creates a form to delete a Tournament entity.
     *
     * @param Tournament $tournament The Tournament entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Tournament $tournament)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tournament_delete', ['id' => $tournament->getId()]))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

    /**
     * Deletes a Tournament entity.
     *
     * @Route("edit/{id}", name="tournament_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Tournament $tournament)
    {
        $form = $this->createDeleteForm($tournament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($tournament);
            $em->flush();
        }

        return $this->redirectToRoute('my_tournaments');
    }
}


