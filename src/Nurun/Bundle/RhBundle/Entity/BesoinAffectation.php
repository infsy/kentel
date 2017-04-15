<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * BesoinAffectation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\BesoinAffectationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class BesoinAffectation
{
    use ORMBehaviors\Blameable\Blameable;

        /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\TypeBesoin", inversedBy="besoinsAffectation")
     * @ORM\JoinColumn(nullable=false)
     */
    private $typeBesoin;

    /**
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Client", inversedBy="besoinsAffectation")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Mandat", inversedBy="besoinsAffectation")
     * @ORM\JoinColumn(nullable=true)
     */
    private $mandat;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_debut", type="datetime", nullable=false)
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_fin", type="datetime", nullable=false)
     */
    private $dateFin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_requise", type="date", nullable=false)
     */
    private $dateRequise;

    /**
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\PrioriteBesoin")
     * @ORM\JoinColumn(nullable=false)
     */
    private $prioriteBesoin;

    /**
     * @var string
     *
     * @ORM\Column(name="contexte", type="text", unique=false, nullable=true)
     */
    private $contexte;

    /**
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\ProfilConseiller", inversedBy="besoinsAffectation")
     * @ORM\JoinColumn(nullable=false)
     */
    private $profil;

    /**
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Niveau")
     * @ORM\JoinColumn(nullable=false)
     */
    private $niveauCompetence;

    /**
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Niveau")
     * @ORM\JoinColumn(nullable=false)
     */
    private $niveauMobilite;

    /**
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Niveau")
     * @ORM\JoinColumn(nullable=false)
     */
    private $niveauLangue;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="text", unique=false, nullable=true)
     */
    private $commentaire;

    /**
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Conseiller")
     * @ORM\JoinColumn(nullable=true)
     */
    private $propositionAffectation;

    /**
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\SourceAffectation")
     * @ORM\JoinColumn(nullable=true)
     */
    private $sourceAffectation;

    /**
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\StatutAffectation")
     * @ORM\JoinColumn(nullable=false)
     */
    private $statutAffectation;

    /**
     * @var boolean
     *
     * @ORM\Column(name="penalite", type="boolean", nullable=true)
     */
    private $penalite;

    /**
     * @var string
     *
     * @ORM\Column(name="budget", type="text", nullable=true)
     */
    private $budget;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Set createdAt
     *
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function __toString() {
        return ($this->getDescription());
    }

    /**
     * Set updatedAt
     *
     * @ORM\PreUpdate
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }

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
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     *
     * @return BesoinAffectation
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     *
     * @return BesoinAffectation
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set dateRequise
     *
     * @param \DateTime $dateRequise
     *
     * @return BesoinAffectation
     */
    public function setDateRequise($dateRequise)
    {
        $this->dateRequise = $dateRequise;

        return $this;
    }

    /**
     * Get dateRequise
     *
     * @return \DateTime
     */
    public function getDateRequise()
    {
        return $this->dateRequise;
    }

    /**
     * Get auteur
     *
     * @return string
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * Set contexte
     *
     * @param string $contexte
     *
     * @return BesoinAffectation
     */
    public function setContexte($contexte)
    {
        $this->contexte = $contexte;

        return $this;
    }

    /**
     * Get contexte
     *
     * @return string
     */
    public function getContexte()
    {
        return $this->contexte;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return BesoinAffectation
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set typeBesoin
     *
     * @param \Nurun\Bundle\RhBundle\Entity\TypeBesoin $typeBesoin
     *
     * @return BesoinAffectation
     */
    public function setTypeBesoin(\Nurun\Bundle\RhBundle\Entity\TypeBesoin $typeBesoin)
    {
        $this->typeBesoin = $typeBesoin;

        return $this;
    }

    /**
     * Get typeBesoin
     *
     * @return \Nurun\Bundle\RhBundle\Entity\TypeBesoin
     */
    public function getTypeBesoin()
    {
        return $this->typeBesoin;
    }

    /**
     * Set client
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Client $client
     *
     * @return BesoinAffectation
     */
    public function setClient(\Nurun\Bundle\RhBundle\Entity\Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \Nurun\Bundle\RhBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set mandat
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Mandat $mandat
     *
     * @return BesoinAffectation
     */
    public function setMandat(\Nurun\Bundle\RhBundle\Entity\Mandat $mandat = null)
    {
        $this->mandat = $mandat;

        return $this;
    }

    /**
     * Get mandat
     *
     * @return \Nurun\Bundle\RhBundle\Entity\Mandat
     */
    public function getMandat()
    {
        return $this->mandat;
    }


    /**
     * Set profil
     *
     * @param \Nurun\Bundle\RhBundle\Entity\ProfilConseiller $profil
     *
     * @return BesoinAffectation
     */
    public function setProfil(\Nurun\Bundle\RhBundle\Entity\ProfilConseiller $profil)
    {
        $this->profil = $profil;

        return $this;
    }

    /**
     * Get profil
     *
     * @return \Nurun\Bundle\RhBundle\Entity\ProfilConseiller
     */
    public function getProfil()
    {
        return $this->profil;
    }

    /**
     * Set niveauCompetence
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Niveau $niveauCompetence
     *
     * @return BesoinAffectation
     */
    public function setNiveauCompetence(\Nurun\Bundle\RhBundle\Entity\Niveau $niveauCompetence)
    {
        $this->niveauCompetence = $niveauCompetence;

        return $this;
    }

    /**
     * Get niveauCompetence
     *
     * @return \Nurun\Bundle\RhBundle\Entity\Niveau
     */
    public function getNiveauCompetence()
    {
        return $this->niveauCompetence;
    }

    /**
     * Set niveauMobilite
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Niveau $niveauMobilite
     *
     * @return BesoinAffectation
     */
    public function setNiveauMobilite(\Nurun\Bundle\RhBundle\Entity\Niveau $niveauMobilite)
    {
        $this->niveauMobilite = $niveauMobilite;

        return $this;
    }

    /**
     * Get niveauMobilite
     *
     * @return \Nurun\Bundle\RhBundle\Entity\Niveau
     */
    public function getNiveauMobilite()
    {
        return $this->niveauMobilite;
    }

    /**
     * Set niveauLangue
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Niveau $niveauLangue
     *
     * @return BesoinAffectation
     */
    public function setNiveauLangue(\Nurun\Bundle\RhBundle\Entity\Niveau $niveauLangue)
    {
        $this->niveauLangue = $niveauLangue;

        return $this;
    }

    /**
     * Get niveauLangue
     *
     * @return \Nurun\Bundle\RhBundle\Entity\Niveau
     */
    public function getNiveauLangue()
    {
        return $this->niveauLangue;
    }

    /**
     * Set propositionAffectation
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Conseiller $propositionAffectation
     *
     * @return BesoinAffectation
     */
    public function setPropositionAffectation(\Nurun\Bundle\RhBundle\Entity\Conseiller $propositionAffectation = null)
    {
        $this->propositionAffectation = $propositionAffectation;

        return $this;
    }

    /**
     * Get propositionAffectation
     *
     * @return \Nurun\Bundle\RhBundle\Entity\Conseiller
     */
    public function getPropositionAffectation()
    {
        return $this->propositionAffectation;
    }

    /**
     * Set sourceAffectation
     *
     * @param \Nurun\Bundle\RhBundle\Entity\SourceAffectation $sourceAffectation
     *
     * @return BesoinAffectation
     */
    public function setSourceAffectation(\Nurun\Bundle\RhBundle\Entity\SourceAffectation $sourceAffectation = null)
    {
        $this->sourceAffectation = $sourceAffectation;

        return $this;
    }

    /**
     * Get sourceAffectation
     *
     * @return \Nurun\Bundle\RhBundle\Entity\SourceAffectation
     */
    public function getSourceAffectation()
    {
        return $this->sourceAffectation;
    }

    /**
     * Set statutAffectation
     *
     * @param \Nurun\Bundle\RhBundle\Entity\StatutAffectation $statutAffectation
     *
     * @return BesoinAffectation
     */
    public function setStatutAffectation(\Nurun\Bundle\RhBundle\Entity\StatutAffectation $statutAffectation)
    {
        $this->statutAffectation = $statutAffectation;

        return $this;
    }

    /**
     * Get statutAffectation
     *
     * @return \Nurun\Bundle\RhBundle\Entity\StatutAffectation
     */
    public function getStatutAffectation()
    {
        return $this->statutAffectation;
    }

    /**
     * Set prioriteBesoin
     *
     * @param \Nurun\Bundle\RhBundle\Entity\PrioriteBesoin $prioriteBesoin
     *
     * @return BesoinAffectation
     */
    public function setPrioriteBesoin(\Nurun\Bundle\RhBundle\Entity\PrioriteBesoin $prioriteBesoin)
    {
        $this->prioriteBesoin = $prioriteBesoin;

        return $this;
    }

    /**
     * Get prioriteBesoin
     *
     * @return \Nurun\Bundle\RhBundle\Entity\PrioriteBesoin
     */
    public function getPrioriteBesoin()
    {
        return $this->prioriteBesoin;
    }

    /**
     * Set penalite
     *
     * @param boolean $penalite
     *
     * @return BesoinAffectation
     */
    public function setPenalite($penalite)
    {
        $this->penalite = $penalite;

        return $this;
    }

    /**
     * Get penalite
     *
     * @return boolean
     */
    public function getPenalite()
    {
        return $this->penalite;
    }

    /**
     * Set budget
     *
     * @param string $budget
     *
     * @return BesoinAffectation
     */
    public function setBudget($budget)
    {
        $this->budget = $budget;

        return $this;
    }

    /**
     * Get budget
     *
     * @return string
     */
    public function getBudget()
    {
        return $this->budget;
    }
}
