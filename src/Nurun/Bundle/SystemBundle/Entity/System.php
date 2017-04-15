<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Nurun\Bundle\SystemBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * 
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\SystemBundle\Entity\SystemRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class System {
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
     * @Assert\Email()
     * @ORM\Column(name="emailvpas", type="text", nullable = true)
     */
    private $emailVpas;
    
    /**
     * @var string
     * @Assert\Email()
     * @ORM\Column(name="emailvpts", type="text", nullable = true)
     */
    private $emailVpts;
    
    /**
     * @var string
     * @Assert\Email()
     * @ORM\Column(name="emailvpsi", type="text", nullable = true)
     */
    private $emailVpsi;
    
    /**
     * @var string
     * @Assert\Email()
     * @ORM\Column(name="emailadmin", type="text", nullable = true)
     */
    private $emailAdmin;

    /**
     * @var string
     * @Assert\Email()
     * @ORM\Column(name="emailadministrateur", type="text", nullable = true)
     */
    private $emailAdministrateur;

    /**
     * @var string
     * @Assert\Email()
     * @ORM\Column(name="emailvacances", type="text", nullable = true)
     */
    private $emailGestionVacances;

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
     * Set emailVpas
     *
     * @param string $emailVpas
     *
     * @return System
     */
    public function setEmailVpas($emailVpas)
    {
        $this->emailVpas = $emailVpas;

        return $this;
    }

    /**
     * Get emailVpas
     *
     * @return string
     */
    public function getEmailVpas()
    {
        return $this->emailVpas;
    }

    /**
     * Set emailVpts
     *
     * @param string $emailVpts
     *
     * @return System
     */
    public function setEmailVpts($emailVpts)
    {
        $this->emailVpts = $emailVpts;

        return $this;
    }

    /**
     * Get emailVpts
     *
     * @return string
     */
    public function getEmailVpts()
    {
        return $this->emailVpts;
    }

    /**
     * Set emailVpsi
     *
     * @param string $emailVpsi
     *
     * @return System
     */
    public function setEmailVpsi($emailVpsi)
    {
        $this->emailVpsi = $emailVpsi;

        return $this;
    }

    /**
     * Get emailVpsi
     *
     * @return string
     */
    public function getEmailVpsi()
    {
        return $this->emailVpsi;
    }

    /**
     * Set emailAdmin
     *
     * @param string $emailAdmin
     *
     * @return System
     */
    public function setEmailAdmin($emailAdmin)
    {
        $this->emailAdmin = $emailAdmin;

        return $this;
    }

    /**
     * Get emailAdmin
     *
     * @return string
     */
    public function getEmailAdmin()
    {
        return $this->emailAdmin;
    }

    /**
     * Set emailGestionVacances
     *
     * @param string $emailGestionVacances
     *
     * @return System
     */
    public function setEmailGestionVacances($emailGestionVacances)
    {
        $this->emailGestionVacances = $emailGestionVacances;

        return $this;
    }

    /**
     * Get emailGestionVacances
     *
     * @return string
     */
    public function getEmailGestionVacances()
    {
        return $this->emailGestionVacances;
    }

    /**
     * Set emailAdministrateur
     *
     * @param string $emailAdministrateur
     *
     * @return System
     */
    public function setEmailAdministrateur($emailAdministrateur)
    {
        $this->emailAdministrateur = $emailAdministrateur;

        return $this;
    }

    /**
     * Get emailAdministrateur
     *
     * @return string
     */
    public function getEmailAdministrateur()
    {
        return $this->emailAdministrateur;
    }
}
