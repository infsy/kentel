<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Mandat
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\MandatRepository")
 */
class Mandat
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
     * @var string
     *
     * @ORM\Column(name="identifiant", type="string", length=90, unique=true)
     */
    private $identifiant;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="text", unique=false, nullable=true)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="secteur", type="string", length=10, unique=false, nullable=true)
     */
    private $secteur;

    /**
     * @ORM\ManyToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\Adresse", inversedBy="mandats")
     * @ORM\JoinTable(name="mandats_adresses")
     */
    private $adresses;

    /**
     * @ORM\ManyToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\Conseiller", inversedBy="mandatsCoordination")
     * @ORM\JoinTable(name="mandats_coordonnateurs")
     */
    private $coordonnateurs;

    /**
     * @var string
     *
     * @ORM\Column(name="offre", type="string", length=30, unique=false, nullable=true)
     */
    private $offre;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=2, unique=false, nullable=true)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_fin", type="date", nullable=true)
     */
    private $dateFin;

    /**
     * @var decimal
     *
     * @ORM\Column(name="nbreheures", type="decimal", precision=3, scale=1)
     */
    private $nbreHeures;

    /**
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Client", inversedBy="mandats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\ConseillerMandat", mappedBy="mandat", cascade={"persist"})
     */
    private $conseillers;

    /**
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Conseiller", inversedBy="mandataires")
     * @ORM\JoinColumn(nullable=true)
     */
    private $mandataire;

    /**
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Conseiller", inversedBy="chargeProjets")
     * @ORM\JoinColumn(nullable=true)
     */
    private $chargeprojet;

    /**
     * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\BesoinAffectation", mappedBy="mandat")
     */
    private $besoinsAffectation;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="text", unique=false, nullable=true)
     */
    private $commentaire;

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
     * @return Mandat
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
     * Set client
     *
     * @param \Nurun\RhBundle\Entity\Client $client
     * @return Mandat
     */
    public function setClient(\Nurun\Bundle\RhBundle\Entity\Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \Nurun\RhBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set mandataire
     *
     * @param \Nurun\PRhBundle\Entity\Conseiller $mandataire
     * @return Mandat
     */
    public function setMandataire(\Nurun\Bundle\RhBundle\Entity\Conseiller $mandataire = null)
    {
        $this->mandataire = $mandataire;

        return $this;
    }

    /**
     * Get mandataire
     *
     * @return \Nurun\RhBundle\Entity\Conseiller
     */
    public function getMandataire()
    {
        return $this->mandataire;
    }

    /**
     * Set chargeprojet
     *
     * @param \Nurun\RhBundle\Entity\Conseiller $chargeprojet
     * @return Mandat
     */
    public function setChargeprojet(\Nurun\Bundle\RhBundle\Entity\Conseiller $chargeprojet = null)
    {
        $this->chargeprojet = $chargeprojet;

        return $this;
    }

    /**
     * Get chargeprojet
     *
     * @return \Nurun\RhBundle\Entity\Conseiller
     */
    public function getChargeprojet()
    {
        return $this->chargeprojet;
    }

    /**
     * Set titre
     *
     * @param string $titre
     * @return Mandat
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->conseillers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->adresses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->nbreHeures = 35;
    }

    public function getDisplay()
    {
        return ($this->getClient()->getAcronyme() . "-" . $this->getIdentifiant());
    }

    public function __toString()
    {
        return ($this->getClient()->getAcronyme() . "-" . $this->getIdentifiant());
    }

    /**
     * Add conseillers
     *
     * @param \Nurun\Bundle\RhBundle\Entity\ConseillerMandat $conseillers
     * @return Mandat
     */
    public function addConseiller(\Nurun\Bundle\RhBundle\Entity\ConseillerMandat $conseillers)
    {
        $this->conseillers[] = $conseillers;

        return $this;
    }

    /**
     * Remove conseillers
     *
     * @param \Nurun\Bundle\RhBundle\Entity\ConseillerMandat $conseillers
     */
    public function removeConseiller(\Nurun\Bundle\RhBundle\Entity\ConseillerMandat $conseillers)
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
     * Set nbreHeures
     *
     * @param string $nbreHeures
     * @return Mandat
     */
    public function setNbreHeures($nbreHeures)
    {
        $this->nbreHeures = $nbreHeures;

        return $this;
    }

    /**
     * Get nbreHeures
     *
     * @return string
     */
    public function getNbreHeures()
    {
        return $this->nbreHeures;
    }

    /**
     * Set secteur
     *
     * @param string $secteur
     * @return Mandat
     */
    public function setSecteur($secteur)
    {
        $this->secteur = $secteur;

        return $this;
    }

    /**
     * Get secteur
     *
     * @return string
     */
    public function getSecteur()
    {
        return $this->secteur;
    }

    /**
     * Set offre
     *
     * @param string $offre
     * @return Mandat
     */
    public function setOffre($offre)
    {
        $this->offre = $offre;

        return $this;
    }

    /**
     * Get offre
     *
     * @return string
     */
    public function getOffre()
    {
        return $this->offre;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Mandat
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     * @return Mandat
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
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return Mandat
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
     * Add besoinsAffectation
     *
     * @param \Nurun\Bundle\RhBundle\Entity\BesoinAffectation $besoinsAffectation
     *
     * @return Mandat
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
     * Add adress
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Adresse $adress
     *
     * @return Mandat
     */
    public function addAdress(\Nurun\Bundle\RhBundle\Entity\Adresse $adress)
    {
        $this->adresses[] = $adress;

        return $this;
    }

    /**
     * Remove adress
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Adresse $adress
     */
    public function removeAdress(\Nurun\Bundle\RhBundle\Entity\Adresse $adress)
    {
        $this->adresses->removeElement($adress);
    }

    /**
     * Get adresses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdresses()
    {
        return $this->adresses;
    }

    /**
     * Add coordonnateur
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Conseiller $coordonnateur
     *
     * @return Mandat
     */
    public function addCoordonnateur(\Nurun\Bundle\RhBundle\Entity\Conseiller $coordonnateur)
    {
        $this->coordonnateurs[] = $coordonnateur;

        return $this;
    }

    /**
     * Remove coordonnateur
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Conseiller $coordonnateur
     */
    public function removeCoordonnateur(\Nurun\Bundle\RhBundle\Entity\Conseiller $coordonnateur)
    {
        $this->coordonnateurs->removeElement($coordonnateur);
    }

    /**
     * Get coordonnateurs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCoordonnateurs()
    {
        return $this->coordonnateurs;
    }
}
