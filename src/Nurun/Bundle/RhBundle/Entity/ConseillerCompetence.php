<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConseillerCompetence
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\ConseillerCompetenceRepository")
 */

class ConseillerCompetence {

     /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
   * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Conseiller", inversedBy="competences")
   * @ORM\JoinColumn(nullable=false)
    */
      private $conseiller;
      
    /**
   * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Competence", inversedBy="conseillers")
   * @ORM\JoinColumn(nullable=false)
    */
      private $competence;

    /**
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Niveau")
     * @ORM\JoinColumn(nullable=false)
     */
    private $niveau;
    
    /**
     * Constructor
     */
    public function __construct()
    {
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
     * Set conseiller
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Conseiller $conseiller
     *
     * @return ConseillerCompetence
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
     * Set competence
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Competence $competence
     *
     * @return ConseillerCompetence
     */
    public function setCompetence(\Nurun\Bundle\RhBundle\Entity\Competence $competence)
    {
        $this->competence = $competence;

        return $this;
    }

    /**
     * Get competence
     *
     * @return \Nurun\Bundle\RhBundle\Entity\Competence
     */
    public function getCompetence()
    {
        return $this->competence;
    }

    /**
     * Set niveau
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Niveau $niveau
     *
     * @return ConseillerCompetence
     */
    public function setNiveau(\Nurun\Bundle\RhBundle\Entity\Niveau $niveau)
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * Get niveau
     *
     * @return \Nurun\Bundle\RhBundle\Entity\Niveau
     */
    public function getNiveau()
    {
        return $this->niveau;
    }
}
