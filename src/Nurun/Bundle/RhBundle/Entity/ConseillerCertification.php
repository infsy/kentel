<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConseillerCertification
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\ConseillerCertificationRepository")
 */

class ConseillerCertification {

     /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
   * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Conseiller", inversedBy="certifications")
   * @ORM\JoinColumn(nullable=false)
    */
      private $conseiller;
      
    /**
   * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Certification", inversedBy="conseillers")
   * @ORM\JoinColumn(nullable=false)
    */
      private $certification;
      
      /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->date = new \DateTime();
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
     * Set date
     *
     * @param \DateTime $date
     * @return ConseillerDiplome
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set conseiller
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Conseiller $conseiller
     * @return ConseillerDiplome
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
     * Set certification
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Certification $certification
     *
     * @return ConseillerCertification
     */
    public function setCertification(\Nurun\Bundle\RhBundle\Entity\Certification $certification)
    {
        $this->certification = $certification;

        return $this;
    }

    /**
     * Get certification
     *
     * @return \Nurun\Bundle\RhBundle\Entity\Certification
     */
    public function getCertification()
    {
        return $this->certification;
    }
}
