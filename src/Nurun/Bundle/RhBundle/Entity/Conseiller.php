<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
//use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Conseiller
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\ConseillerRepository")
 */
class Conseiller
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
     * @var integer
     *
     * @ORM\Column(name="num_employe", type="integer")
     */
    private $numEmploye;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\PosteConseiller")
     * @ORM\JoinColumn(nullable=true)
     */
    private $poste;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\ProfilConseiller",cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $profil;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\ProfilConseiller",cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $profilSecondaire;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\RoleConseiller")
     * @ORM\JoinColumn(nullable=true)
     */
    private $role;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\StatutConseiller")
     * @ORM\JoinColumn(nullable=true)
     */
    private $statut;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\VicePresidence")
     * @ORM\JoinColumn(nullable=true)
     */
    private $vicePresidence;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Domaine")
     * @ORM\JoinColumn(nullable=true)
     */
    private $domaine;

    /**
     * @var string
     * @Assert\Email()
     * @ORM\Column(name="courriel", type="string", length=255, nullable = true)
     */
    private $courriel;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_arrivee", type="date", nullable = true)
     */
    private $dateArrivee;

    /**
     * @var integer
     *
     * @ORM\Column(name="exp_annees", type="integer", nullable = true)
     */
    private $experienceAnnees;

    /**
     * @var integer
     *
     * @ORM\Column(name="exp_mois", type="integer", nullable = true)
     */
    private $experienceMois;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone_mandat", type="string", length=255, nullable = true)
     */
    private $telephoneMandat;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone_nurun", type="string", length=255, nullable = true)
     */
    private $telephoneNurun;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone_autres", type="string", length=255, nullable = true)
     */
    private $telephoneAutres;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_depart", type="date", nullable=true)
     */
    private $dateDepart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_fete", type="date", nullable=true)
     */
    private $dateFete;

    /**
     * @var boolean
     *
     * @ORM\Column(name="actif", type="boolean")
     */
    private $actif;

    /**
     * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\ConseillerDiplome", mappedBy="conseiller", cascade={"remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $diplomes;

    /**
     * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\ConseillerCompetence", mappedBy="conseiller", cascade={"remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $competences;

    /**
     * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\ConseillerCertification", mappedBy="conseiller", cascade={"remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $certifications;

    /**
     * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\ConseillerRdp", mappedBy="conseiller", cascade={"persist"})
     */
    private $rdps;

    /**
     * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\ConseillerMandat", mappedBy="conseiller", cascade={"remove"})
     */
    private $mandats;

    /**
     * @ORM\ManyToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\Mandat", mappedBy="coordonnateurs")
     * @ORM\JoinColumn(nullable=true)
     */
    private $mandatsCoordination;

    /**
     * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\Mandat", mappedBy="mandataire")
     */
    private $mandataires;

    /**
     * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\Mandat", mappedBy="chargeprojet")
     */
    private $chargeProjets;

    /**
     * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\ConseillerLanguage", mappedBy="conseiller", cascade={"remove"})
     */
    private $languages;

    /**
     * @var text
     * @ORM\Column(name="consigne", type="text", nullable=true)
     */
    private $consigne;

    /**
     * @var text
     * @ORM\Column(name="contextes", type="text", nullable=true)
     */
    private $contextes;

    /**
     * @var decimal
     *
     * @ORM\Column(name="nbreheures", type="decimal", precision=3, scale=1)
     */
    private $nbreHeures;

    /**
     * @var blob
     *
     * @ORM\Column(name="photo", type="blob", nullable=true)
     */
    private $photo;

    public function __construct()
    {
        // Par défaut, la date d'arrivée est la date d'aujourd'hui
        $this->dateArrivee = new \DateTime();
        $this->actif = true;
        $this->mandats = new ArrayCollection();
        $this->rdps = new ArrayCollection();
        $this->certifications = new ArrayCollection();
        $this->diplomes = new ArrayCollection();
        $this->competences = new ArrayCollection();
        $this->mandataires = new ArrayCollection();
        $this->chargeProjets = new ArrayCollection();
    }

    public function __toString()
    {
        return ($this->getPrenom() . " " . $this->getNom());
    }

    public function getDisplay()
    {
        return ($this->getPrenom() . " " . $this->getNom());
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
     * Set prenom
     *
     * @param string $prenom
     * @return Conseiller
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Conseiller
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set dateArrivee
     *
     * @param \DateTime $dateArrivee
     * @return Conseiller
     */
    public function setDateArrivee($dateArrivee)
    {
        $this->dateArrivee = $dateArrivee;

        return $this;
    }

    /**
     * Get dateArrivee
     *
     * @return \DateTime
     */
    public function getDateArrivee()
    {
        return $this->dateArrivee;
    }

    /**
     * Set dateDepart
     *
     * @param \DateTime $dateDepart
     * @return Conseiller
     */
    public function setDateDepart($dateDepart)
    {
        $this->dateDepart = $dateDepart;

        return $this;
    }

    /**
     * Get dateDepart
     *
     * @return \DateTime
     */
    public function getDateDepart()
    {
        return $this->dateDepart;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     * @return Conseiller
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Set nbreheures
     *
     * @param integer $nbreheures
     * @return Conseiller
     */
    public function setNbreHeures($nbreheures)
    {
        $this->nbreHeures = $nbreheures;

        return $this;
    }

    /**
     * Get nbreheures
     *
     * @return integer
     */
    public function getNbreHeures()
    {
        return $this->nbreHeures;
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return Conseiller
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        if(!is_null($this->photo)){
            return stream_get_contents($this->photo);
        }
        return $this->photo;
    }

    /**
     * Add rdps
     *
     * @param \Nurun\Bundle\RhBundle\Entity\ConseillerRdp $rdps
     * @return Conseiller
     */
    public function addRdp(\Nurun\Bundle\RhBundle\Entity\ConseillerRdp $rdps)
    {
        $this->rdps[] = $rdps;

        return $this;
    }

    /**
     * Remove rdps
     *
     * @param \Nurun\Bundle\RhBundle\Entity\ConseillerRdp $rdps
     */
    public function removeRdp(\Nurun\Bundle\RhBundle\Entity\ConseillerRdp $rdps)
    {
        $this->rdps->removeElement($rdps);
    }

    /**
     * Get rdps
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRdps()
    {
        return $this->rdps;
    }

    /**
     * Add mandats
     *
     * @param \Nurun\Bundle\RhBundle\Entity\ConseillerMandat $mandats
     * @return Conseiller
     */
    public function addMandat(\Nurun\Bundle\RhBundle\Entity\ConseillerMandat $mandats)
    {
        $this->mandats[] = $mandats;

        return $this;
    }

    /**
     * Remove mandats
     *
     * @param \Nurun\Bundle\RhBundle\Entity\ConseillerMandat $mandats
     */
    public function removeMandat(\Nurun\Bundle\RhBundle\Entity\ConseillerMandat $mandats)
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
     * Set diplome
     *
     * @param string $diplome
     * @return Conseiller
     */
    public function setDiplome($diplome)
    {
        $this->diplome = $diplome;

        return $this;
    }

    /**
     * Get diplome
     *
     * @return string
     */
    public function getDiplome()
    {
        return $this->diplome;
    }

    /**
     * Set poste
     *
     * @param string $poste
     * @return Conseiller
     */
    public function setPoste($poste)
    {
        $this->poste = $poste;

        return $this;
    }

    /**
     * Get poste
     *
     * @return string
     */
    public function getPoste()
    {
        return $this->poste;
    }

    /**
     * Set domaine
     *
     * @param string $domaine
     * @return Conseiller
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
     * Add diplomes
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Diplome $diplomes
     * @return Conseiller
     */
    public function addDiplome(\Nurun\Bundle\RhBundle\Entity\Diplome $diplomes)
    {
        $this->diplomes[] = $diplomes;

        return $this;
    }

    /**
     * Remove diplomes
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Diplome $diplomes
     */
    public function removeDiplome(\Nurun\Bundle\RhBundle\Entity\Diplome $diplomes)
    {
        $this->diplomes->removeElement($diplomes);
    }

    /**
     * Get diplomes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDiplomes()
    {
        return $this->diplomes;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return Conseiller
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set courriel
     *
     * @param string $courriel
     * @return Conseiller
     */
    public function setCourriel($courriel)
    {
        $this->courriel = $courriel;

        return $this;
    }

    /**
     * Get courriel
     *
     * @return string
     */
    public function getCourriel()
    {
        return $this->courriel;
    }

    /**
     * Set statut
     *
     * @param string $statut
     * @return Conseiller
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return string
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set dateFete
     *
     * @param \DateTime $dateFete
     * @return Conseiller
     */
    public function setDateFete($dateFete)
    {
        $this->dateFete = $dateFete;

        return $this;
    }

    /**
     * Get dateFete
     *
     * @return \DateTime
     */
    public function getDateFete()
    {
        return $this->dateFete;
    }

    /**
     * Set vicePresidence
     *
     * @param \Nurun\Bundle\RhBundle\Entity\VicePresidence $vicePresidence
     * @return Conseiller
     */
    public function setVicePresidence(\Nurun\Bundle\RhBundle\Entity\VicePresidence $vicePresidence = null)
    {
        $this->vicePresidence = $vicePresidence;

        return $this;
    }

    /**
     * Get vicePresidence
     *
     * @return \Nurun\Bundle\RhBundle\Entity\VicePresidence
     */
    public function getVicePresidence()
    {
        return $this->vicePresidence;
    }

    /**
     * Set numEmploye
     *
     * @param integer $numEmploye
     *
     * @return Conseiller
     */
    public function setNumEmploye($numEmploye)
    {
        $this->numEmploye = $numEmploye;

        return $this;
    }

    /**
     * Get numEmploye
     *
     * @return integer
     */
    public function getNumEmploye()
    {
        return $this->numEmploye;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Conseiller
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set telephoneMandat
     *
     * @param string $telephoneMandat
     *
     * @return Conseiller
     */
    public function setTelephoneMandat($telephoneMandat)
    {
        $this->telephoneMandat = $telephoneMandat;

        return $this;
    }

    /**
     * Get telephoneMandat
     *
     * @return string
     */
    public function getTelephoneMandat()
    {
        return $this->telephoneMandat;
    }

    /**
     * Set telephoneNurun
     *
     * @param string $telephoneNurun
     *
     * @return Conseiller
     */
    public function setTelephoneNurun($telephoneNurun)
    {
        $this->telephoneNurun = $telephoneNurun;

        return $this;
    }

    /**
     * Get telephoneNurun
     *
     * @return string
     */
    public function getTelephoneNurun()
    {
        return $this->telephoneNurun;
    }

    /**
     * Add competence
     *
     * @param \Nurun\Bundle\RhBundle\Entity\ConseillerCompetence $competence
     *
     * @return Conseiller
     */
    public function addCompetence(\Nurun\Bundle\RhBundle\Entity\ConseillerCompetence $competence)
    {
        $this->competences[] = $competence;

        return $this;
    }

    /**
     * Remove competence
     *
     * @param \Nurun\Bundle\RhBundle\Entity\ConseillerCompetence $competence
     */
    public function removeCompetence(\Nurun\Bundle\RhBundle\Entity\ConseillerCompetence $competence)
    {
        $this->competences->removeElement($competence);
    }

    /**
     * Get competences
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompetences()
    {
        return $this->competences;
    }

    /**
     * Add certification
     *
     * @param \Nurun\Bundle\RhBundle\Entity\ConseillerCertification $certification
     *
     * @return Conseiller
     */
    public function addCertification(\Nurun\Bundle\RhBundle\Entity\ConseillerCertification $certification)
    {
        $this->certifications[] = $certification;

        return $this;
    }

    /**
     * Remove certification
     *
     * @param \Nurun\Bundle\RhBundle\Entity\ConseillerCertification $certification
     */
    public function removeCertification(\Nurun\Bundle\RhBundle\Entity\ConseillerCertification $certification)
    {
        $this->certifications->removeElement($certification);
    }

    /**
     * Get certifications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCertifications()
    {
        return $this->certifications;
    }

    /**
     * Set telephoneAutres
     *
     * @param string $telephoneAutres
     *
     * @return Conseiller
     */
    public function setTelephoneAutres($telephoneAutres)
    {
        $this->telephoneAutres = $telephoneAutres;

        return $this;
    }

    /**
     * Get telephoneAutres
     *
     * @return string
     */
    public function getTelephoneAutres()
    {
        return $this->telephoneAutres;
    }

    /**
     * Set consigne
     *
     * @param string $consigne
     *
     * @return Conseiller
     */
    public function setConsigne($consigne)
    {
        $this->consigne = $consigne;

        return $this;
    }

    /**
     * Get consigne
     *
     * @return string
     */
    public function getConsigne()
    {
        return $this->consigne;
    }

    /**
     * Set posteSecondaire
     *
     * @param \Nurun\Bundle\RhBundle\Entity\PosteConseiller $posteSecondaire
     *
     * @return Conseiller
     */
    public function setPosteSecondaire(\Nurun\Bundle\RhBundle\Entity\PosteConseiller $posteSecondaire = null)
    {
        $this->posteSecondaire = $posteSecondaire;

        return $this;
    }

    /**
     * Get posteSecondaire
     *
     * @return \Nurun\Bundle\RhBundle\Entity\PosteConseiller
     */
    public function getPosteSecondaire()
    {
        return $this->posteSecondaire;
    }

    /**
     * Add language
     *
     * @param \Nurun\Bundle\RhBundle\Entity\ConseillerLanguage $language
     *
     * @return Conseiller
     */
    public function addLanguage(\Nurun\Bundle\RhBundle\Entity\ConseillerLanguage $language)
    {
        $this->language[] = $language;

        return $this;
    }

    /**
     * Remove language
     *
     * @param \Nurun\Bundle\RhBundle\Entity\ConseillerLanguage $language
     */
    public function removeLanguage(\Nurun\Bundle\RhBundle\Entity\ConseillerLanguage $language)
    {
        $this->language->removeElement($language);
    }

    /**
     * Get language
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Get languages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Set experienceAnnees
     *
     * @param integer $experienceAnnees
     *
     * @return Conseiller
     */
    public function setExperienceAnnees($experienceAnnees)
    {
        $this->experienceAnnees = $experienceAnnees;

        return $this;
    }

    /**
     * Get experienceAnnees
     *
     * @return integer
     */
    public function getExperienceAnnees()
    {
        return $this->experienceAnnees;
    }

    /**
     * Set experienceMois
     *
     * @param integer $experienceMois
     *
     * @return Conseiller
     */
    public function setExperienceMois($experienceMois)
    {
        $this->experienceMois = $experienceMois;

        return $this;
    }

    /**
     * Get experienceMois
     *
     * @return integer
     */
    public function getExperienceMois()
    {
        return $this->experienceMois;
    }

    /**
     * Set contextes
     *
     * @param string $contextes
     *
     * @return Conseiller
     */
    public function setContextes($contextes)
    {
        $this->contextes = $contextes;

        return $this;
    }

    /**
     * Get contextes
     *
     * @return string
     */
    public function getContextes()
    {
        return $this->contextes;
    }

    /**
     * Set profil
     *
     * @param \Nurun\Bundle\RhBundle\Entity\ProfilConseiller $profil
     *
     * @return Conseiller
     */
    public function setProfil(\Nurun\Bundle\RhBundle\Entity\ProfilConseiller $profil = null)
    {
        $this->profil = $profil;

        return $this;
    }

    /**
     * Get profil
     *
     * @return \Nurun\Bundle\RhBundle\Entity\ProfilConseiller
     */
    public function getProfil()
    {
        return $this->profil;
    }

    /**
     * Set profilSecondaire
     *
     * @param \Nurun\Bundle\RhBundle\Entity\ProfilConseiller $profilSecondaire
     *
     * @return Conseiller
     */
    public function setProfilSecondaire(\Nurun\Bundle\RhBundle\Entity\ProfilConseiller $profilSecondaire = null)
    {
        $this->profilSecondaire = $profilSecondaire;

        return $this;
    }

    /**
     * Get profilSecondaire
     *
     * @return \Nurun\Bundle\RhBundle\Entity\ProfilConseiller
     */
    public function getProfilSecondaire()
    {
        return $this->profilSecondaire;
    }

    /**
     * Add mandataires
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Mandat $mandataires
     * @return Conseiller
     */
    public function addMandataire(\Nurun\Bundle\RhBundle\Entity\Mandat $mandataires)
    {
        $this->mandataires[] = $mandataires;

        return $this;
    }

    /**
     * Remove mandataires
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Mandat $mandataires
     */
    public function removeMandataire(\Nurun\Bundle\RhBundle\Entity\Mandat $mandataires)
    {
        $this->mandataires->removeElement($mandataires);
    }

    /**
     * Get mandataires
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMandataires()
    {
        return $this->mandataires;
    }

    /**
     * Add chargeProjets
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Mandat $chargeProjets
     * @return Conseiller
     */
    public function addChargeProjet(\Nurun\Bundle\RhBundle\Entity\Mandat $chargeProjets)
    {
        $this->chargeProjets[] = $chargeProjets;

        return $this;
    }

    /**
     * Remove chargeProjets
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Mandat $chargeProjets
     */
    public function removeChargeProjet(\Nurun\Bundle\RhBundle\Entity\Mandat $chargeProjets)
    {
        $this->chargeProjets->removeElement($chargeProjets);
    }

    /**
     * Get chargeProjets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChargeProjets()
    {
        return $this->chargeProjets;
    }

    /**
     * Add mandatsCoordination
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Mandat $mandatsCoordination
     *
     * @return Conseiller
     */
    public function addMandatsCoordination(\Nurun\Bundle\RhBundle\Entity\Mandat $mandatsCoordination)
    {
        $this->mandatsCoordination[] = $mandatsCoordination;

        return $this;
    }

    /**
     * Remove mandatsCoordination
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Mandat $mandatsCoordination
     */
    public function removeMandatsCoordination(\Nurun\Bundle\RhBundle\Entity\Mandat $mandatsCoordination)
    {
        $this->mandatsCoordination->removeElement($mandatsCoordination);
    }

    /**
     * Get mandatsCoordination
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMandatsCoordination()
    {
        return $this->mandatsCoordination;
    }
}
