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
        $repository = $this->getDoctrine()
            ->getRepository('TournamentBundle:Participation');

        $participation = new Participation();
        $form = $this->createForm('TournamentBundle\Form\Type\ParticipationType', $participation);

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
