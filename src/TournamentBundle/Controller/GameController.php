<?php

namespace TournamentBundle\Controller;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TournamentBundle\Entity\Game;
use TournamentBundle\Entity\Tournament;
use RegisterBundle\Entity\User;

class GameController extends Controller
{
    private $possition = [0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13,14,14,15,15,16,16];
    /**
     * @Route("/games", name="list_games")
     */
    public function listGamesAction()
    {
        $gameEM = $this->getDoctrine()->getManager();

        $query = $gameEM->createQuery(
            'SELECT g
             FROM TournamentBundle:Game g
             WHERE g.player1 = :userId or g.player2 = :userId
             ORDER BY g.id ASC'
        )->setParameter('userId', $this->getUser()->getId());

        $games = $query->getResult();

        return $this->render('TournamentBundle:Game:list_games.html.twig', array(
            "games"     => $games,
            "user_id"   => $this->getUser()->getId(),
        ));
    }

    /**
     * @Route("/games/score/{id}/{result}", name="score")
     */
    public function scoreAction($id, $result)
    {
        $gameEM = $this->getDoctrine()->getManager();
        $game = $gameEM->getRepository('TournamentBundle:Game')->find($id);

        if($game->getPlayer1() == $this->getUser()) {
            if ($game->getResult2() == $result) {
                $query = $gameEM->createQuery(
                    'UPDATE TournamentBundle:Game g
                     SET g.result1 = null, g.result2 = null, g.options =1
                     WHERE g.id = :gameId'
                )
                    ->setParameter('gameId', $id);
                $games = $query->execute();
            } else {
                $game->setResult1((int)$result);
                if ($game->getResult2() != null)
                    $game->setOptions(false);
                $gameEM->flush();
                //add new row if it is possible
                $game = $gameEM->getRepository('TournamentBundle:Game')->find($id);
                if ($game->getPosition() % 2 == 0)
                ///górny w parze
                {
                    $game2 = $gameEM->getRepository('TournamentBundle:Game')->findOneBy([
                        'tournament' => $game->getTournament(),
                        'position' => $game->getPosition() + 1,
                        'round' => $game->getRound(),
                    ]);
                    $scores[] = $game->getResult1();
                    $scores[] = $game->getResult2();
                    $scores[] = $game2->getResult1();
                    $scores[] = $game2->getResult2();

                    $passed = true;
                    foreach ($scores as $score)
                        if ($score == null)
                            $passed = false;

                    if ($passed) {
                        $newGame = new Game();
                        $newGame->setTournament($game->getTournament());
                        if ($scores[0] == 1)
                            $newGame->setPlayer1($game->getPlayer1());
                        else
                            $newGame->setPlayer1($game->getPlayer2());

                        if ($scores[2] == 1)
                            $newGame->setPlayer2($game2->getPlayer1());
                        else
                            $newGame->setPlayer2($game2->getPlayer2());
                        $newGame->setRound($game->getRound() + 1);
                        $newGame->setPosition($this->possition[$game->getPosition()]);
                        $newGame->setOptions(false);

                        $gameEM->persist($newGame);
                        $gameEM->flush();
                    }
                } else //dolny w parze
                {
                    $game2 = $gameEM->getRepository('TournamentBundle:Game')->findOneBy([
                        'tournament' => $game->getTournament(),
                        'position' => $game->getPosition() - 1,
                        'round' => $game->getRound(),
                    ]);
                    $scores[] = $game2->getResult1();
                    $scores[] = $game2->getResult2();
                    $scores[] = $game->getResult1();
                    $scores[] = $game->getResult2();

                    $passed = true;
                    foreach ($scores as $score)
                        if ($score == null)
                            $passed = false;

                    if ($passed) {
                        $newGame = new Game();
                        $newGame->setTournament($game->getTournament());
                        if ($scores[0] == 1)
                            $newGame->setPlayer1($game->getPlayer1());
                        else
                            $newGame->setPlayer1($game->getPlayer2());

                        if ($scores[2] == 1)
                            $newGame->setPlayer2($game2->getPlayer1());
                        else
                            $newGame->setPlayer2($game2->getPlayer2());
                        $newGame->setRound($game->getRound() + 1);
                        $newGame->setPosition($this->possition[$game->getPosition()]);
                        $newGame->setOptions(false);

                        $gameEM->persist($newGame);
                        $gameEM->flush();
                    }
                }
            }
        }
        elseif ($game->getPlayer2() == $this->getUser())
        {
            if ($game->getResult1() == $result) {
                $query = $gameEM->createQuery(
                    'UPDATE TournamentBundle:Game g
                     SET g.result1 = null, g.result2 = null, g.options =1
                     WHERE g.id = :gameId'
                )
                    ->setParameter('gameId', $id);
                $games = $query->execute();
            } else {
                $game->setResult2((int)$result);
                if ($game->getResult1() != null)
                    $game->setOptions(false);
                $gameEM->flush();
                //add new row if it is possible
                $game = $gameEM->getRepository('TournamentBundle:Game')->find($id);
                if ($game->getPosition() % 2 == 0)
                    ///górny w parze
                {
                    $game2 = $gameEM->getRepository('TournamentBundle:Game')->findOneBy([
                        'tournament' => $game->getTournament(),
                        'position' => $game->getPosition() + 1,
                        'round' => $game->getRound(),
                    ]);
                    $scores[] = $game->getResult1();
                    $scores[] = $game->getResult2();
                    $scores[] = $game2->getResult1();
                    $scores[] = $game2->getResult2();

                    $passed = true;
                    foreach ($scores as $score)
                        if ($score == null)
                            $passed = false;

                    if ($passed) {
                        $newGame = new Game();
                        $newGame->setTournament($game->getTournament());
                        if ($scores[0] == 1)
                            $newGame->setPlayer1($game->getPlayer1());
                        else
                            $newGame->setPlayer1($game->getPlayer2());

                        if ($scores[2] == 1)
                            $newGame->setPlayer2($game2->getPlayer1());
                        else
                            $newGame->setPlayer2($game2->getPlayer2());
                        $newGame->setRound($game->getRound() + 1);
                        $newGame->setPosition($this->possition[$game->getPosition()]);
                        $newGame->setOptions(false);

                        $gameEM->persist($newGame);
                        $gameEM->flush();
                    }
                } else //dolny w parze
                {
                    $game2 = $gameEM->getRepository('TournamentBundle:Game')->findOneBy([
                        'tournament' => $game->getTournament(),
                        'position' => $game->getPosition() - 1,
                        'round' => $game->getRound(),
                    ]);
                    $scores[] = $game2->getResult1();
                    $scores[] = $game2->getResult2();
                    $scores[] = $game->getResult1();
                    $scores[] = $game->getResult2();

                    $passed = true;
                    foreach ($scores as $score)
                        if ($score == null)
                            $passed = false;

                    if ($passed) {
                        $newGame = new Game();
                        $newGame->setTournament($game->getTournament());
                        if ($scores[0] == 1)
                            $newGame->setPlayer1($game->getPlayer1());
                        else
                            $newGame->setPlayer1($game->getPlayer2());

                        if ($scores[2] == 1)
                            $newGame->setPlayer2($game2->getPlayer1());
                        else
                            $newGame->setPlayer2($game2->getPlayer2());
                        $newGame->setRound($game->getRound() + 1);
                        $newGame->setPosition($this->possition[$game->getPosition()]);
                        $newGame->setOptions(false);

                        $gameEM->persist($newGame);
                        $gameEM->flush();
                    }
                }
            }
        }

        return $this->redirect($this->generateUrl('list_games'));
    }

//     /**
//      * @Route("/games/new", name="score")
//      * @Method({"GET", "POST"})
//      */
//    public function newGameAction(Request $request)
//    {
//        $game = new Game();
//        $form = $this->createFormBuilder($game)
//            ->add('tournament', EntityType::class, [
//                'label' => "Turniej",
//                'class' => 'TournamentBundle:Tournament',
//                'choice_label' => 'name',
//            ])
//            ->add('player1', EntityType::class, [
//                'label' => "Zawodnik 1",
//                'class' => 'RegisterBundle:User',
//                'choice_label' => 'fullname',
//            ])
//            ->add('player2', EntityType::class, [
//                'label' => "Zawodnik 2",
//                'class' => 'RegisterBundle:User',
//                'choice_label' => 'fullname',
//            ])
//            ->add('round',  IntegerType::class, ['label' => "runda"])
//            ->add('position',IntegerType::class, ['label' => "pozycja"])
//            ->getForm();
//
//        $form->handleRequest($request);
//        if ($form->isValid()) {
//
//            $game = $form->getData();
//            $game->setOptions(0);
//
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($game);
//            $em->flush();
//        }
//        return $this->render('TournamentBundle:Game:add.html.twig', [
//            'form' => $form->createView(),
//        ]);
//    }
}
