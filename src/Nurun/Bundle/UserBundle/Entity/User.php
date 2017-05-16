<?php

/*
 * Copyright (c) 2015, cedric
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * * Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */
namespace Nurun\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="user")
 */

class User extends BaseUser
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $language;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $departement;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $vp;

    /**
     * @ORM\OneToOne(targetEntity="Nurun\Bundle\UserBundle\Entity\Permissions", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $permissions;

    /**
     * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\UserNotification", mappedBy="user")
     * @ORM\JoinColumn(nullable=true)
     */
    private $userNotifications;

    /**
     * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\UserFonction", mappedBy="user")
     * @ORM\JoinColumn(nullable=true)
     */
    private $userFonctions;

    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @ORM\PostLoad
     */

      public function composeVp()
      {
        $departement = $this->getDepartement();
        if ($this->getEmail() != 'cedric.thibault@nurun.com')
        {
            switch ($departement)
            {
                case "Solutions informatiques":
                    $this->setVp("VPSI");
                break;
                case "Sécurité et technologies":
                    $this->setVp("VPTS");
                break;
                case "Affaires et stratégies":
                    $this->setVp("VPAS");
                break;
                case "Affaires et strategies":
                    $this->setVp("VPAS");
                break;
                case "Ressources Humaines":
                    $this->setVp("VPOF");
                break;
                case "Direction Générale":
                    $this->setVp("VPOF");
                break;
                case "Opérations et finances":
                    $this->setVp("VPOF");
                break;
            }
        }
      }


    public function __construct()
    {
       parent::__construct();
       if (empty($this->roles)) {
         $this->roles[] = 'ROLE_USER';
         $this->setPermissions(new Permissions());
       }
       $this->userNotifications = new \Doctrine\Common\Collections\ArrayCollection();
       $this->userFonctions = new \Doctrine\Common\Collections\ArrayCollection();
    }
    public function setDn($dn) {
        $this->dn = $dn;
    }
    public function getDn() {
        return $this->dn;
    }
    public function setName($name) {
        $this->name = $name;
    }
    public function setLanguage($language) {
        $this->language = $language;
    }


    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set departement
     *
     * @param string $departement
     *
     * @return User
     */
    public function setDepartement($departement)
    {
        $this->departement = $departement;

        return $this;
    }

    /**
     * Get departement
     *
     * @return string
     */
    public function getDepartement()
    {
        return $this->departement;
    }

    /**
     * Set vp
     *
     * @param string $vp
     *
     * @return User
     */
    public function setVp($vp)
    {
        $this->vp = $vp;

        return $this;
    }

    /**
     * Get vp
     *
     * @return string
     */
    public function getVp()
    {
        return $this->vp;
    }

    /**
     * Set permissions
     *
     * @param \Nurun\Bundle\UserBundle\Entity\Permissions $permissions
     *
     * @return User
     */
    public function setPermissions(\Nurun\Bundle\UserBundle\Entity\Permissions $permissions = null)
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * Get permissions
     *
     * @return \Nurun\Bundle\UserBundle\Entity\Permissions
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Add userNotification
     *
     * @param \Nurun\Bundle\RhBundle\Entity\UserNotification $userNotification
     *
     * @return Permission
     */
    public function addUserNotification(\Nurun\Bundle\RhBundle\Entity\UserNotification $userNotification)
    {
        $this->userNotifications[] = $userNotification;

        return $this;
    }

    /**
     * Remove userNotification
     *
     * @param \Nurun\Bundle\RhBundle\Entity\UserNotification $userNotification
     */
    public function removeUserNotification(\Nurun\Bundle\RhBundle\Entity\UserNotification $userNotification)
    {
        $this->userNotifications->removeElement($userNotification);
    }

    /**
     * Get userNotifications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserNotifications()
    {
        return $this->userNotifications;
    }

    /**
     * Add userFonction
     *
     * @param \Nurun\Bundle\RhBundle\Entity\UserFonction $userFonction
     *
     * @return Permission
     */
    public function addUserFonction(\Nurun\Bundle\RhBundle\Entity\UserFonction $userFonction)
    {
        $this->userFonctions[] = $userFonction;

        return $this;
    }

    /**
     * Remove userFonction
     *
     * @param \Nurun\Bundle\RhBundle\Entity\UserFonction $userFonction
     */
    public function removeUserFonction(\Nurun\Bundle\RhBundle\Entity\UserFonction $userFonction)
    {
        $this->userFonctions->removeElement($userFonction);
    }

    /**
     * Get userFonctions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserFonctions()
    {
        return $this->userFonctions;
    }
}
