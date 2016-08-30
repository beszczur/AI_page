<?php

namespace TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
        $participationRepository = $this->getDoctrine()
            ->getRepository('TournamentBundle:Participation');

        $tournamentRepository = $this->getDoctrine()
            ->getRepository('TournamentBundle:Tournament');

        $participation = new Participation();
        $form = $this->createForm('TournamentBundle\Form\Type\ParticipationType', $participation);

        $form->handleRequest($request);
        if ($form->isValid()) {

            $participation = $form->getData();
            $participation->setTournament($tournamentRepository->find($id));
            $participation->setUser($this->getUser());

            $registeredNumber = count($participationRepository->findBy(array('tournament' => $id)));

            if ($registeredNumber < $tournamentRepository->find($id)->getParticipantsLimit())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($participation);
                $em->flush();

                return $this->redirect($this->generateUrl('show_tournament',
                    array('id' => $id)));
                /*Przekierowanie użytkownika po udanym zgłoszeniu formularza uniemożliwia użytkownikowi, by odświeżył i ponownie przesłał dane.*/

            }
            else
            {
                return $this->render('TournamentBundle:Errors:limit_exceed.html.twig');
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
        return $this->render('TournamentBundle:Participation:my_participations.html.twig', array(
            // ...
        ));
    }

}
