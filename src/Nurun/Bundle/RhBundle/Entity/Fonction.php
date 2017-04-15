<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fonction
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\FonctionRepository")
 */
class Fonction
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\UserFonction", mappedBy="fonction")
     * @ORM\JoinColumn(nullable=true)
     */
    private $userFonctions;

    /**
     * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\FonctionPermission", mappedBy="fonction")
     * @ORM\JoinColumn(nullable=true)
     */
    private $fonctionPermissions;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userFonctions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->fonctionPermissions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Fonction
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Set description
     *
     * @param string $description
     *
     * @return Fonction
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
     * Add userFonction
     *
     * @param \Nurun\Bundle\RhBundle\Entity\UserFonction $userFonction
     *
     * @return Fonction
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

    /**
     * Add fonctionPermission
     *
     * @param \Nurun\Bundle\RhBundle\Entity\FonctionPermission $fonctionPermission
     *
     * @return Fonction
     */
    public function addFonctionPermission(\Nurun\Bundle\RhBundle\Entity\FonctionPermission $fonctionPermission)
    {
        $this->fonctionPermissions[] = $fonctionPermission;

        return $this;
    }

    /**
     * Remove fonctionPermission
     *
     * @param \Nurun\Bundle\RhBundle\Entity\FonctionPermission $fonctionPermission
     */
    public function removeFonctionPermission(\Nurun\Bundle\RhBundle\Entity\FonctionPermission $fonctionPermission)
    {
        $this->fonctionPermissions->removeElement($fonctionPermission);
    }

    /**
     * Get fonctionPermissions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFonctionPermissions()
    {
        return $this->fonctionPermissions;
    }
}
