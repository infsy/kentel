<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * ConseillerRdp
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\ConseillerRdpRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ConseillerRdp
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
   * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Conseiller", inversedBy ="rdps")
   * @ORM\JoinColumn(nullable=false)
   */
      private $conseiller;
      
  /**
   * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Conseiller")
   * @ORM\JoinColumn(nullable=false)
   */
      private $rdp;
      
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
     * @return ConseillerRdp
     */
    public function setConseiller(\Nurun\Bundle\RhBundle\Entity\Conseiller $conseiller)
    {
        $this->conseiller = $conseiller;

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
     * Set rdp
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Conseiller $rdp
     * @return ConseillerRdp
     */
    public function setRdp(\Nurun\Bundle\RhBundle\Entity\Conseiller $rdp)
    {
        $this->rdp = $rdp;

        return $this;
    }

    /**
     * Get rdp
     *
     * @return \Nurun\RhBundle\Entity\Conseiller 
     */
    public function getRdp()
    {
        return $this->rdp;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return ConseillerRdp
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
     * @return ConseillerRdp
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
}
