<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Certification
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\CertificationRepository")
 */
class Certification
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="acronyme", type="string", length=25, unique=false, nullable=true)
     */
    private $acronyme;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, unique=true)
     */
    private $description;

    /**
    * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\ConseillerCertification", mappedBy="certification", cascade={"persist"})
    * @ORM\JoinColumn(nullable=true)
    */
    private $conseillers;
    
    /**
     * @var string
     *
     * @ORM\Column(name="fournisseur", type="string", length=255, nullable=true)
     */
    private $fournisseur;
    
    
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
     * Set acronyme
     *
     * @param string $acronyme
     *
     * @return Certification
     */
    public function setAcronyme($acronyme)
    {
        $this->acronyme = $acronyme;

        return $this;
    }

    /**
     * Get acronyme
     *
     * @return string
     */
    public function getAcronyme()
    {
        return $this->acronyme;
    }

    /**
     * Set fournisseur
     *
     * @param string $fournisseur
     *
     * @return Certification
     */
    public function setFournisseur($fournisseur)
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get fournisseur
     *
     * @return string
     */
    public function getFournisseur()
    {
        return $this->fournisseur;
    }
}
