<?php
/**
 * Created by PhpStorm.
 * User: beszczur
 * Date: 13.08.16
 * Time: 23:23
 */

namespace TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="tournaments")
 * @ORM\HasLifecycleCallbacks
 */
class Tournament
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $name;
    /**
     * @ORM\Column(type="date")
     */
    protected $registrationEndDate;
    /**
     * @ORM\Column(type="date")
     * @Assert\GreaterThanOrEqual("today")
     */
    protected $tournamentDate;
    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $city;
    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $street;
    /**
     * @ORM\Column(type="string", length=500)
     */
    protected $description;
    /**
     * @ORM\Column(type="integer")
     */
    protected $participantsLimit;
    /**
     * @ORM\Column(type="array")
     */
    private $sponsorLogoPaths;
    /**
     * @ORM\ManyToOne(targetEntity="RegisterBundle\Entity\User", inversedBy="tournaments")
     * @ORM\JoinColumn(name="organizer_id", referencedColumnName="id")
     */
    private $organizer;
    /**
     * @ORM\OneToOne(targetEntity="TournamentBundle\Entity\Discipline")
     * @ORM\JoinColumn(name="discipline_id", referencedColumnName="id")
     */
    private $discipline;

    private $files;


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
     * Set name
     *
     * @param string $name
     *
     * @return Tournament
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set tournamentDate
     *
     * @param \day $tournamentDate
     *
     * @return Tournament
     */
    public function setTournamentDate(\day $tournamentDate)
    {
        $this->tournamentDate = $tournamentDate;

        return $this;
    }

    /**
     * Get tournamentDate
     *
     * @return \day
     */
    public function getTournamentDate()
    {
        return $this->tournamentDate;
    }

    /**
     * Set localization
     *
     * @param string $localization
     *
     * @return Tournament
     */
    public function setLocalization($localization)
    {
        $this->localization = $localization;

        return $this;
    }

    /**
     * Get localization
     *
     * @return string
     */
    public function getLocalization()
    {
        return $this->localization;
    }

    /**
     * Set participantsLimit
     *
     * @param integer $participantsLimit
     *
     * @return Tournament
     */
    public function setParticipantsLimit($participantsLimit)
    {
        $this->participantsLimit = $participantsLimit;

        return $this;
    }

    /**
     * Get participantsLimit
     *
     * @return integer
     */
    public function getParticipantsLimit()
    {
        return $this->participantsLimit;
    }

    /**
     * Set organizer
     *
     * @param \RegisterBundle\Entity\User $organizer
     *
     * @return Tournament
     */
    public function setOrganizer(\RegisterBundle\Entity\User $organizer = null)
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * Get organizer
     *
     * @return \RegisterBundle\Entity\User
     */
    public function getOrganizer()
    {
        return $this->organizer;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Tournament
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set street
     *
     * @param string $street
     *
     * @return Tournament
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Tournament
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set registrationEndDate
     *
     * @param \DateTime $registrationEndDate
     *
     * @return Tournament
     */
    public function setRegistrationEndDate($registrationEndDate)
    {
        $this->registrationEndDate = $registrationEndDate;

        return $this;
    }

    /**
     * Get registrationEndDate
     *
     * @return \DateTime
     */
    public function getRegistrationEndDate()
    {
        return $this->registrationEndDate;
    }

    /**
     * Set discipline
     *
     * @param \TournamentBundle\Entity\Discipline $discipline
     *
     * @return Tournament
     */
    public function setDiscipline(\TournamentBundle\Entity\Discipline $discipline = null)
    {
        $this->discipline = $discipline;

        return $this;
    }

    /**
     * Get discipline
     *
     * @return \TournamentBundle\Entity\Discipline
     */
    public function getDiscipline()
    {
        return $this->discipline;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function setFiles(array $files)
    {
        $this->files = $files;
    }






    /**
     * Constructor
     */
    public function __construct()
    {
        $this->files = array();
        $this->sponsorLogoPaths = array();
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @ORM\PreFlush()
     */
    public function upload()
    {
        $this->paths = array();
        foreach($this->files as $file)
        {
            if($file != null)
            {
                $path = sha1(uniqid(mt_rand(), true)).'.'.$file->guessExtension();
                array_push ($this->sponsorLogoPaths, $path);
                $file->move($this->getUploadRootDir(), $path);
            }


            unset($file);
        }
    }

    /**
     * Set Sponsor Logo Paths
     *
     * @param array $paths
     *
     * @return Tournament
     */
    public function setSponsorLogoPaths($paths)
    {
        $this->sponsorLogoPaths = $paths;

        return $this;
    }

    /**
     * Get paths
     *
     * @return array
     */
    public function getSponsorLogoPaths()
    {
        return $this->sponsorLogoPaths;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads';
    }

}