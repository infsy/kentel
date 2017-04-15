<?php

namespace Nurun\Bundle\RhBundle\Entity;
use APY\DataGridBundle\Grid\Mapping as GRID;

use Doctrine\ORM\Mapping as ORM;

/**
 * Diplome
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\DiplomeRepository")
 */
class Diplome
{
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
     * @var string
     *
     * @ORM\Column(name="niveau", type="string", length=25, unique=false, nullable=true)
     * @GRID\Column(operatorsVisible=false, title="Niveau")
     */
    private $niveau;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, unique=true)
     * @GRID\Column(operatorsVisible=false, title="Description")
     */
    private $description;

    /**
    * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\ConseillerDiplome", mappedBy="diplome", cascade={"persist"})
    * @ORM\JoinColumn(nullable=true)
    * @GRID\Column(visible=false)
    */    
    private $conseillers;
    
    /**
     * @var string
     *
     * @ORM\Column(name="domaine", type="string", length=255, nullable=true)
     * @GRID\Column(visible=false)
     */
    private $domaine;
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
    }

    public function __toString() {
        return ($this->getDescription());
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
     * Set identifiant
     *
     * @param string $identifiant
     * @return Diplome
     */
    public function setIdentifiant($identifiant)
    {
        $this->identifiant = $identifiant;

        return $this;
    }

    /**
     * Get identifiant
     *
     * @return string 
     */
    public function getIdentifiant()
    {
        return $this->identifiant;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Diplome
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
     * Set domaine
     *
     * @param string $domaine
     * @return Diplome
     */
    public function setDomaine($domaine)
    {
        $this->domaine = $domaine;

        return $this;
    }

    /**
     * Get domaine
     *
     * @return string 
     */
    public function getDomaine()
    {
        return $this->domaine;
    }

    /**
     * Add conseillers
     *
     * @param \Nurun\Bundle\RhBundle\Entity\ConseillerDiplome $conseillers
     * @return Diplome
     */
    public function addConseiller(\Nurun\Bundle\RhBundle\Entity\ConseillerDiplome $conseillers)
    {
        $this->conseillers[] = $conseillers;

        return $this;
    }

    /**
     * Remove conseillers
     *
     * @param \Nurun\Bundle\RhBundle\Entity\ConseillerDiplome $conseillers
     */
    public function removeConseiller(\Nurun\Bundle\RhBundle\Entity\ConseillerDiplome $conseillers)
    {
        $this->conseillers->removeElement($conseillers);
    }

    /**
     * Get conseillers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConseillers()
    {
        return $this->conseillers;
    }

    /**
     * Set niveau
     *
     * @param string $niveau
     * @return Diplome
     */
    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * Get niveau
     *
     * @return string 
     */
    public function getNiveau()
    {
        return $this->niveau;
    }
}
