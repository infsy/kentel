<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * ProfilConseiller
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\ProfilConseillerRepository")
 */
class ProfilConseiller
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
     * @ORM\Column(name="profil", type="string", length=100, nullable=false)
     */
    private $profil;


    /**
     * @var string
     *
     * @ORM\Column(name="profilen", type="string", length=100, nullable=false)
     */
    private $profilEN;


    /**
     * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\BesoinAffectation", mappedBy="profil")
     */
    private $besoinsAffectation;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->besoinsAffectation = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return ($this->getProfil());
    }

    /**
     * Get profil
     *
     * @return string
     */
    public function getProfil()
    {
        return $this->profil;
    }

    /**
     * Set profil
     *
     * @param string $profil
     *
     * @return ProfilConseiller
     */
    public function setProfil($profil)
    {
        $this->profil = $profil;

        return $this;
    }

    public function getDisplay()
    {
        return ($this->getProfil());
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
     * Add besoinsAffectation
     *
     * @param \Nurun\Bundle\RhBundle\Entity\BesoinAffectation $besoinsAffectation
     *
     * @return ProfilConseiller
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

    /**
     * Get profilEN
     *
     * @return string
     */
    public function getProfilEN()
    {
        return $this->profilEN;
    }

    /**
     * Set profilEN
     *
     * @param string $profilEN
     *
     * @return ProfilConseiller
     */
    public function setProfilEN($profilEN)
    {
        $this->profilEN = $profilEN;

        return $this;
    }
}
