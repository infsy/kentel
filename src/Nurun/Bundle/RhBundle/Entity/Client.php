<?php

namespace Nurun\Bundle\RhBundle\Entity;
use APY\DataGridBundle\Grid\Mapping as GRID;

use Doctrine\ORM\Mapping as ORM;

/**
 * Client
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\ClientRepository")
 */
class Client
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
     * @ORM\Column(name="identifiant", type="string", length=255, unique=false)
     * @GRID\Column(visible=true, operatorsVisible=false, title="Client", filterable=false) 
     */
    private $identifiant;

    /**
     * @var string
     *
     * @ORM\Column(name="acronyme", type="string", length=40, unique=true)
     * @GRID\Column(visible=true, operatorsVisible=false, title="Acronyme", filterable=false) 
     */
    private $acronyme;

    /**
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\TypeClient", inversedBy="clients")
     * @ORM\JoinColumn(nullable=true)
     */
    private $typeClient;

    /**
     * @ORM\OrderBy({"identifiant" = "ASC"})
     * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\Mandat", mappedBy="client")
     */
    private $mandats;

    /**
     * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\BesoinAffectation", mappedBy="client")
     */
    private $besoinsAffectation;

    public function __toString() {
        return ($this->getAcronyme());
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
     * @return Client
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
     * Set acronyme
     *
     * @param string $acronyme
     * @return Client
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
     * Constructor
     */
    public function __construct()
    {
        $this->mandats = new \Doctrine\Common\Collections\ArrayCollection();
        $this->clients = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add mandats
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Mandat $mandats
     * @return Client
     */
    public function addMandat(\Nurun\Bundle\RhBundle\Entity\Mandat $mandats)
    {
        $this->mandats[] = $mandats;

        return $this;
    }

    /**
     * Remove mandats
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Mandat $mandats
     */
    public function removeMandat(\Nurun\Bundle\RhBundle\Entity\Mandat $mandats)
    {
        $this->mandats->removeElement($mandats);
    }

    /**
     * Get mandats
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMandats()
    {
        return $this->mandats;
    }

    /**
     * Set typeClient
     *
     * @param \Nurun\Bundle\RhBundle\Entity\TypeClient $typeClient
     *
     * @return Client
     */
    public function setTypeClient(\Nurun\Bundle\RhBundle\Entity\TypeClient $typeClient = null)
    {
        $this->typeClient = $typeClient;

        if($typeClient != null){
            $typeClient->addClient($this);
        }

        return $this;
    }

    /**
     * Get typeClient
     *
     * @return \Nurun\Bundle\RhBundle\Entity\TypeClient
     */
    public function getTypeClient()
    {
        return $this->typeClient;
    }

    /**
     * Add besoinsAffectation
     *
     * @param \Nurun\Bundle\RhBundle\Entity\BesoinAffectation $besoinsAffectation
     *
     * @return Client
     */
    public function addBesoinsAffectation(\Nurun\Bundle\RhBundle\Entity\BesoinAffectation $besoinsAffectation)
    {
        $this->besoinsAffectation[] = $besoinsAffectation;

        return $this;
    }

    /**
     * Remove besoinsAffectation
     *
     * @param \Nurun\Bundle\RhBundle\Entity\BesoinAffectation $besoinsAffectation
     */
    public function removeBesoinsAffectation(\Nurun\Bundle\RhBundle\Entity\BesoinAffectation $besoinsAffectation)
    {
        $this->besoinsAffectation->removeElement($besoinsAffectation);
    }

    /**
     * Get besoinsAffectation
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBesoinsAffectation()
    {
        return $this->besoinsAffectation;
    }
}
