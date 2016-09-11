<?php

namespace TournamentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
            "games" => $games,
            "user_id"     => $this->getUser()->getId(),
        ));
    }

    /**
     * @Route("/games/score/{id}/{result}", name="score")
     */
    public function scoreAction($id, $result)
    {
        $gameEM = $this->getDoctrine()->getManager();
        $game = $gameEM->getRepository('TournamentBundle:Game')->find($id);

        if($game->getPlayer1() == $this->getUser())
        {
            if($game->getResult2() == $result)
            {
                $query = $gameEM->createQuery(
                    'UPDATE TournamentBundle:Game g
                     SET g.result1 = null, g.result2 = null
                     WHERE g.id = :gameId'
                )
                    ->setParameter('gameId', $id);

                $games = $query->execute();
            }
            else
            {
                $game->setResult1((int)$result);
                if($game->getResult2() != null)
                    $game->setOption(false);
                $gameEM->flush();
                //TODo: add new row if it is possible
                $game = $gameEM->getRepository('TournamentBundle:Game')->find($id);
                if($game->getPosition()%2 == 0)
                {
                    $game2 = $gameEM->getRepository('TournamentBundle:Game')->findOneBy([
                        'tournament' => $game->getTournament(),
                        'position'   => $game->getPosition()+1,
                        'round'      => $game->getRound(),
                    ]);
                    $scores[] = $game->getResult1();
                    $scores[] = $game->getResult2();
                    $scores[] = $game2->getResult1();
                    $scores[] = $game2->getResult2();

                    $passed = true;
                    foreach ($scores as $score)
                        if($score == null)
                            $passed = false;

                    if($passed)
                    {
//                        $newGame = new Game();
//                        $newGame->setTournament($game->getTournament());
//                        if ($scores[0] == 1)
//                            $newGame->setPlayer1($game->getPlayer1());
//                        else
//                            $newGame->setPlayer1($game->getPlayer2());
//
//                        if ($scores[2] == 1)
//                            $newGame->setPlayer2($game2->getPlayer1());
//                        else
//                            $newGame->setPlayer2($game2->getPlayer2());
//                        $newGame->setResult1(0);
//                        $newGame->setResult2(0);
//                        $newGame->setRound($game->getRound()+1);
//                        $newGame->setPosition($this->possition[$game->getPosition()]);
//                        $newGame->setOption(false);
//
//                        //$em = $this->getDoctrine()->getManager();
//                        $gameEM->persist($newGame);
//                        //var_dump($newGame);
//                        $gameEM->flush();
//                        var_dump('Ble bele bel');

//                        $gameEM = $this->getDoctrine()->getConnection();
//
//                        $query = "INSERT TournamentBundle:Game (`tournament_id`, `player1_id`, `player2_id`, `result1`, `result2`, `round`, `position`, `option`)  VALUES (1,12,31,null,null,2,0,0)";
//                        //->setParameter(['userId', $this->getUser()->getId()]);
//
//                        //$games = $query->getResult();
//                        $stmt = $gameEM->prepare($query);
//                        $stmt->execute();

//                        $db = $this->em->getConnection();
//                        $query = "INSERT INTO table2 (myfield) SELECT table1.myfield FROM table1 WHERE table1.id < 1000";
//                        $stmt = $db->prepare($query);
//                        $params = array();
//                        $stmt->execute($params);
                    }
                }
                else
                {

                }
            }
        }
        elseif ($game->getPlayer2() == $this->getUser())
        {
                //TODO:
        }





        return $this->redirect($this->generateUrl('list_games'));
    }

}
