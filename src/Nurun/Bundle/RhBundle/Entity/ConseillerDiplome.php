<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * ConseillerDiplome
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\ConseillerDiplomeRepository")
 * @GRID\Source(columns="id, conseiller.prenom, conseiller.nom, diplome.identifiant, date")
 */

class ConseillerDiplome {

     /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @GRID\Column(visible=false)
     */
    private $id;

    /**
   * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Conseiller", inversedBy="diplomes")
   * @ORM\JoinColumn(nullable=false)
   * @GRID\Column(field="conseiller.nom", operatorsVisible=false, filterable=false, title="Nom")
   * @GRID\Column(field="conseiller.prenom", operatorsVisible=false, filterable=false, title="Prénom")
   * @GRID\Column(field="conseiller.id", visible=false, filterable=false) 
    */
      private $conseiller;
      
    /**
   * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Diplome", inversedBy="conseillers")
   * @ORM\JoinColumn(nullable=false)
   * @GRID\Column(field="diplome.identifiant", operatorsVisible=false, filterable=false, title="Diplôme")
   * @GRID\Column(field="diplome.id", visible=false, filterable=false) 
    */
      private $diplome;
      
      /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     * @GRID\Column(visible=false)
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
     * Set diplome
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Diplome $diplome
     * @return ConseillerDiplome
     */
    public function setDiplome(\Nurun\Bundle\RhBundle\Entity\Diplome $diplome)
    {
        $this->diplome = $diplome;

        return $this;
    }

    /**
     * Get diplome
     *
     * @return \Nurun\Bundle\RhBundle\Entity\Diplome 
     */
    public function getDiplome()
    {
        return $this->diplome;
    }
}
