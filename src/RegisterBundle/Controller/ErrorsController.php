<?php

namespace RegisterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ErrorsController extends Controller
{
    /**
     * @Route("/errors/limitExceed", name="limit_exceed")
     */
    public function limitExceedAction()
    {
        return $this->render('ErrorPage.html.twig', [
            "page_title"    => "Przekroczono limit zgłoszeń",
            "error_message" => "Limit zgłoszeń na ten turniej został osiągnięty.",
        ]);
    }

    /**
     * @Route("/errors/doubledRegistration", name="more_than_one_registration")
     */
    public function moreThanOneRegistrationAction()
    {
        return $this->render('ErrorPage.html.twig', [
            "page_title"    => "Byłeś już zapisany na ten turniej",
            "error_message" => "Nie można dwukrotnie uczestniczyć w tym samym turnieju. :)",
        ]);
    }

    /**
     * @Route("/errors/duplicatedEmail", name="duplicated_email")
     */
    public function duplicatedEmailAction()
    {
        return $this->render('ErrorPage.html.twig', [
            "page_title"        => "Email istnieje już w bazie",
            "error_message"     => "Użytkownik o podanym adresie email istnieje już w bazie danych. ",
        ]);
    }

    /**
     * @Route("/errors/tournamentNotExists", name="tournament_not_exists")
     */
    public function tournamentNotExistsAction()
    {
        return $this->render('ErrorPage.html.twig', [
            "page_title"        => "Turniej nie istnieje",
            "error_message"     => "Turniej nie istnieje. ;( ",
        ]);
    }

    /**
     * @Route("/errors/powerOfTwoRequired", name="power_of_two_required")
     */
    public function powerOfTwoRequiredAction()
    {
        return $this->render('ErrorPage.html.twig', [
            "page_title"        => "Potęga dwójki wymagana",
            "error_message"     => "Libcza zgłoszonych powinna być potęgą dwójki ",
        ]);
    }

}
