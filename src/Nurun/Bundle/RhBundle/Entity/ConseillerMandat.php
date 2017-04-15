<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * ConseillerMandat
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\ConseillerMandatRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ConseillerMandat
{
    use ORMBehaviors\SoftDeletable\SoftDeletable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
   * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Conseiller", inversedBy="mandats")
   * @ORM\JoinColumn(nullable=false)
    */
      private $conseiller;
      
  /**
   * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Mandat", inversedBy="conseillers")
   * @ORM\JoinColumn(nullable=false)
   */
      private $mandat;
      
      /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_debut", type="date", nullable=false)
     */
    private $dateDebut;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_fin", type="date", nullable=true)
     */
    private $dateFin;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="date", nullable=false)
     */
    private $created;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="date", nullable=true)
     */
    private $updated;
    
    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\StatutAffectation")
     * @ORM\JoinColumn(nullable=true)
     */
    private $statutAffectation;

     /**
     * @var string
     *
     * @ORM\Column(name="identifiantmandat", type="text", length=50, unique=false, nullable=true)
     */
    private $identifiantMandat;
    
    /**
     * @var string
     *
     * @ORM\Column(name="pourcentage", type="smallint", unique=false, nullable=true)
     */
    private $pourcentage;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="text", length=50, unique=false, nullable=true)
     */
    private $commentaire;
    /**
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @ORM\PostLoad
     */
    
      public function composeMandat()
      {
        $acronyme = $this->getMandat()->getClient()->getAcronyme();
        $numero = $this->getMandat()->getIdentifiant();
        $this->setIdentifiantMandat($acronyme.'-'.$numero);
      }
  
    /**
     *
     * @ORM\PrePersist
     */
    public function updateCreated()
    {
        $this->setCreated(new \DateTime('now'));

        if ($this->getUpdated() == null) {
            $this->setUpdated(new \DateTime('now'));
        }
    }

     /**
     *
     * @ORM\PreUpdate
     */
    public function updateUpdated()
    {
        $this->setUpdated(new \DateTime('now'));
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
     * Constructor
     */
    public function __construct()
    {
        $this->dateDebut = new \DateTime();
        $this->dateFin = new \DateTime();
    }

    

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return ConseillerRdp
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
     * @return ConseillerRdp
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
     * Set conseiller
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Conseiller $conseiller
     * @return ConseillerMandat
     */
    public function setConseiller(\Nurun\Bundle\RhBundle\Entity\Conseiller $conseiller)
    {
        $this->conseiller = $conseiller;

        if($conseiller != null){
            $conseiller->addMandat($this);
        }

        return $this;
    }

    /**
     * Get conseiller
     *
     * @return \Nurun\Bundle\RhBundle\Entity\Conseiller 
     */
    public function getConseiller()
    {
        return $this->conseiller;
    }

    /**
     * Set mandat
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Mandat $mandat
     * @return ConseillerMandat
     */
    public function setMandat(\Nurun\Bundle\RhBundle\Entity\Mandat $mandat)
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
     * Set statutAffectation
     *
     * @param string $statutAffectation
     * @return ConseillerMandat
     */
    public function setStatutAffectation($statutAffectation)
    {
        $this->statutAffectation = $statutAffectation;

        return $this;
    }

    /**
     * Get statutAffectation
     *
     * @return string 
     */
    public function getStatutAffectation()
    {
        return $this->statutAffectation;
    }

    /**
     * Set pourcentage
     *
     * @param integer $pourcentage
     * @return ConseillerMandat
     */
    public function setPourcentage($pourcentage)
    {
        $this->pourcentage = $pourcentage;

        return $this;
    }

    /**
     * Get pourcentage
     *
     * @return integer 
     */
    public function getPourcentage()
    {
        return $this->pourcentage;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return ConseillerMandat
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return ConseillerMandat
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set identifiantMandat
     *
     * @param string $identifiantMandat
     * @return ConseillerMandat
     */
    public function setIdentifiantMandat($identifiantMandat)
    {
        $this->identifiantMandat = $identifiantMandat;

        return $this;
    }

    /**
     * Get identifiantMandat
     *
     * @return string 
     */
    public function getIdentifiantMandat()
    {
        return $this->identifiantMandat;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return ConseillerMandat
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
}
