<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Mandat
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\AdresseRepository")
 */
class Adresse
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
     * @var integer
     *
     * @ORM\Column(name="numeroAdresse", type="integer", unique=false, nullable=true)
     */
    private $numeroAdresse;

    /**
     * @var string
     *
     * @ORM\Column(name="ligne1adresse", type="string", length=120, unique=false, nullable=true)
     */
    private $ligne1Adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="ligne2adresse", type="string", length=120, unique=false, nullable=true)
     */
    private $ligne2Adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="ligne3adresse", type="string", length=20, unique=false, nullable=true)
     */
    private $ligne3Adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="codePostal", type="string", length=10, unique=false, nullable=true)
     */
    private $codePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=60, unique=false, nullable=true)
     */
    private $ville;

    /**
     * @var decimal
     *
     * @ORM\Column(name="latitude", type="decimal", precision=18, scale=12, unique=false, nullable=true)
     */
    private $latitude;

    /**
     * @var decimal
     *
     * @ORM\Column(name="longitude", type="decimal", precision=18, scale=12, unique=false, nullable=true)
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="text", unique=false, nullable=true)
     */
    private $commentaire;

    /**
     * @ORM\ManyToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\Mandat", mappedBy="adresses")
     * @ORM\JoinColumn(nullable=true)
     */
    private $mandats;

    public function __construct() {
        $this->mandats = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set numeroAdresse
     *
     * @param integer $numeroAdresse
     *
     * @return Adresse
     */
    public function setNumeroAdresse($numeroAdresse)
    {
        $this->numeroAdresse = $numeroAdresse;

        return $this;
    }

    /**
     * Get numeroAdresse
     *
     * @return integer
     */
    public function getNumeroAdresse()
    {
        return $this->numeroAdresse;
    }

    /**
     * Set ligne1Adresse
     *
     * @param string $ligne1Adresse
     *
     * @return Adresse
     */
    public function setLigne1Adresse($ligne1Adresse)
    {
        $this->ligne1Adresse = $ligne1Adresse;

        return $this;
    }

    /**
     * Get ligne1Adresse
     *
     * @return string
     */
    public function getLigne1Adresse()
    {
        return $this->ligne1Adresse;
    }

    /**
     * Set ligne2Adresse
     *
     * @param string $ligne2Adresse
     *
     * @return Adresse
     */
    public function setLigne2Adresse($ligne2Adresse)
    {
        $this->ligne2Adresse = $ligne2Adresse;

        return $this;
    }

    /**
     * Get ligne2Adresse
     *
     * @return string
     */
    public function getLigne2Adresse()
    {
        return $this->ligne2Adresse;
    }

    /**
     * Set ligne3Adresse
     *
     * @param string $ligne3Adresse
     *
     * @return Adresse
     */
    public function setLigne3Adresse($ligne3Adresse)
    {
        $this->ligne3Adresse = $ligne3Adresse;

        return $this;
    }

    /**
     * Get ligne3Adresse
     *
     * @return string
     */
    public function getLigne3Adresse()
    {
        return $this->ligne3Adresse;
    }

    /**
     * Set codePostal
     *
     * @param string $codePostal
     *
     * @return Adresse
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get codePostal
     *
     * @return string
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return Adresse
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return Adresse
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
     * Set mandat
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Mandat $mandat
     *
     * @return Adresse
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
     * Set latitude
     *
     * @param string $latitude
     *
     * @return Adresse
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     *
     * @return Adresse
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Add mandat
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Mandat $mandat
     *
     * @return Adresse
     */
    public function addMandat(\Nurun\Bundle\RhBundle\Entity\Mandat $mandat)
    {
        $this->mandats[] = $mandat;

        return $this;
    }

    /**
     * Remove mandat
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Mandat $mandat
     */
    public function removeMandat(\Nurun\Bundle\RhBundle\Entity\Mandat $mandat)
    {
        $this->mandats->removeElement($mandat);
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
}
