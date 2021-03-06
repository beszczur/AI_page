<?php

namespace TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="games")
 */
class Game
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="result1", type="integer", nullable=true)
     */
    protected $result1;

    /**
     * @var bool
     *
     * @ORM\Column(name="result2", type="integer", nullable=true)
     */
    protected $result2;

    /**
     * @ORM\ManyToOne(targetEntity="TournamentBundle\Entity\Tournament", inversedBy="games")
     * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id")
     */
    protected $tournament;

    /**
     * @ORM\ManyToOne(targetEntity="RegisterBundle\Entity\User", inversedBy="games")
     * @ORM\JoinColumn(name="player1_id", referencedColumnName="id")
     */
    protected $player1;

    /**
     * @ORM\ManyToOne(targetEntity="RegisterBundle\Entity\User", inversedBy="games")
     * @ORM\JoinColumn(name="player2_id", referencedColumnName="id")
     */
    protected $player2;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $round;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $position;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     * 1 - means that result was deleted because of conflict in players votes
     */
    protected $options;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set result1
     *
     * @param integer $result1
     *
     * @return Game
     */
    public function setResult1($result1)
    {
        $this->result1 = $result1;

        return $this;
    }

    /**
     * Get result1
     *
     * @return integer
     */
    public function getResult1()
    {
        return $this->result1;
    }

    /**
     * Set result2
     *
     * @param integer $result2
     *
     * @return Game
     */
    public function setResult2($result2)
    {
        $this->result2 = $result2;

        return $this;
    }

    /**
     * Get result2
     *
     * @return integer
     */
    public function getResult2()
    {
        return $this->result2;
    }

    /**
     * Set round
     *
     * @param integer $round
     *
     * @return Game
     */
    public function setRound($round)
    {
        $this->round = $round;

        return $this;
    }

    /**
     * Get round
     *
     * @return integer
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Game
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set options
     *
     * @param boolean $options
     *
     * @return Game
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get options
     *
     * @return boolean
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set tournament
     *
     * @param \TournamentBundle\Entity\Tournament $tournament
     *
     * @return Game
     */
    public function setTournament(\TournamentBundle\Entity\Tournament $tournament = null)
    {
        $this->tournament = $tournament;

        return $this;
    }

    /**
     * Get tournament
     *
     * @return \TournamentBundle\Entity\Tournament
     */
    public function getTournament()
    {
        return $this->tournament;
    }

    /**
     * Set player1
     *
     * @param \RegisterBundle\Entity\User $player1
     *
     * @return Game
     */
    public function setPlayer1(\RegisterBundle\Entity\User $player1 = null)
    {
        $this->player1 = $player1;

        return $this;
    }

    /**
     * Get player1
     *
     * @return \RegisterBundle\Entity\User
     */
    public function getPlayer1()
    {
        return $this->player1;
    }

    /**
     * Set player2
     *
     * @param \RegisterBundle\Entity\User $player2
     *
     * @return Game
     */
    public function setPlayer2(\RegisterBundle\Entity\User $player2 = null)
    {
        $this->player2 = $player2;

        return $this;
    }

    /**
     * Get player2
     *
     * @return \RegisterBundle\Entity\User
     */
    public function getPlayer2()
    {
        return $this->player2;
    }
}
