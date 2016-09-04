<?php

namespace TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use TournamentBundle\Entity\Participation;

class ParticipationController extends Controller
{
    /**
     * @Route("/participate/{id}", name="participate_in")
     * @param Request $request
     */
    public function participateInAction($id, Request $request)
    {
        $participationRepository = $this->getDoctrine()->getRepository('TournamentBundle:Participation');
        $tournamentRepository = $this->getDoctrine()->getRepository('TournamentBundle:Tournament');

        $participation = new Participation();
        $form = $this->createForm('TournamentBundle\Form\Type\ParticipationType', $participation);

        if ($this->isUserRegisteredOnTournament($id, $participationRepository))
        {
            return $this->redirect($this->generateUrl('more_than_one_registration'));
        }
        else if ($this->isLimitExceed($id, $participationRepository, $tournamentRepository))
        {
            return $this->redirect($this->generateUrl('limit_exceed'));
        }

        $form->handleRequest($request);
        if ($form->isValid()) {

            if ($this->isUserRegisteredOnTournament($id, $participationRepository))
            {
                return $this->redirect($this->generateUrl('more_than_one_registration'));
            }
            else if ($this->isLimitExceed($id, $participationRepository, $tournamentRepository))
            {
                return $this->redirect($this->generateUrl('limit_exceed'));
            }
            else
            {
                $participation = $form->getData();
                $participation->setTournament($tournamentRepository->find($id));
                $participation->setUser($this->getUser());
                $em = $this->getDoctrine()->getManager();
                $em->persist($participation);
                $em->flush();

                return $this->redirect($this->generateUrl('show_tournament', [
                    'id' => $id
                ]));
                /*Przekierowanie użytkownika po udanym zgłoszeniu formularza uniemożliwia użytkownikowi,
                by odświeżył i ponownie przesłał dane.*/
            }
        }

        return $this->render('TournamentBundle:Participation:participate_in.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/myParticipations")
     */
    public function myParticipationsAction()
    {
        return $this->render('TournamentBundle:Participation:my_participations.html.twig');
    }

    /**
     * @param $id
     * @param $participationRepository
     * @return bool
     */
    private function isUserRegisteredOnTournament($id, $participationRepository)
    {
        return $participationRepository->findBy(['user' => $this->getUser(), "tournament" => $id]) != null;
    }

    /**
     * @param $id
     * @param $participationRepository
     * @param $tournamentRepository
     * @return bool
     */
    private function isLimitExceed($id, $participationRepository, $tournamentRepository)
    {
        return count($participationRepository->findBy(array('tournament' => $id))) >= $tournamentRepository->find($id)->getParticipantsLimit();
    }
}
