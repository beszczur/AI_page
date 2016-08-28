<?php

    namespace TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Participation
 *
 * @ORM\Table(name="participations")
 * @ORM\Entity
 * @UniqueEntity(
 *     fields={"tornament", "user"},
 *     message="Uczestniczysz już w tym turnieju."
 * )
 */
class Participation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="license", type="string", length=50, unique=true)
     */
    private $license;

    /**
     * @var integer
     *
     * @ORM\Column(name="ranking", type="integer")
     */
    private $ranking;

    /**
     * @ORM\ManyToOne(targetEntity="TournamentBundle\Entity\Tournament")
     * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id")
     */
    private $tournament;

    /**
     * @ORM\ManyToOne(targetEntity="RegisterBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

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
     * Set license
     *
     * @param string $license
     *
     * @return Participation
     */
    public function setLicense($license)
    {
        $this->license = $license;

        return $this;
    }

    /**
     * Get license
     *
     * @return string
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * Set ranking
     *
     * @param integer $ranking
     *
     * @return Participation
     */
    public function setRanking($ranking)
    {
        $this->ranking = $ranking;

        return $this;
    }

    /**
     * Get ranking
     *
     * @return integer
     */
    public function getRanking()
    {
        return $this->ranking;
    }

    /**
     * Set tournament
     *
     * @param \TournamentBundle\Entity\Tournament $tournament
     *
     * @return Participation
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
     * Set user
     *
     * @param \RegisterBundle\Entity\User $user
     *
     * @return Participation
     */
    public function setUser(\RegisterBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \RegisterBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
