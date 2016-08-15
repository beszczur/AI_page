<?php

namespace TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ParticipationController extends Controller
{
    /**
     * @Route("/participate", name="participate_in")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function participateInAction(Request $request)
    {
        return $this->render('TournamentBundle:Participation:participate_in.html.twig', array(
            // ...
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
