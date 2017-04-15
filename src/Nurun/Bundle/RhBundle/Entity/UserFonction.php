<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserFonction
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\UserFonctionRepository")
 */
class UserFonction
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
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\UserBundle\Entity\User", inversedBy="userFonctions")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Fonction", inversedBy="userFonctions")
     */
    private $fonction; 


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
     * Set user
     *
     * @param \Nurun\Bundle\UserBundle\Entity\User $user
     *
     * @return UserFonction
     */
    public function setUser(\Nurun\Bundle\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        if($user != null){
            $user->addUserFonction($this);
        }

        return $this;
    }

    /**
     * Get user
     *
     * @return \Nurun\Bundle\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set fonction
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Fonction $fonction
     *
     * @return UserFonction
     */
    public function setFonction(\Nurun\Bundle\RhBundle\Entity\Fonction $fonction = null)
    {
        $this->fonction = $fonction;

        if($fonction != null){
            $fonction->addUserFonction($this);
        }

        return $this;
    }

    /**
     * Get fonction
     *
     * @return \Nurun\Bundle\RhBundle\Entity\Fonction
     */
    public function getFonction()
    {
        return $this->fonction;
    }
}
