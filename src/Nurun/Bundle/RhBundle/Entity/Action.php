<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Action
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\ActionRepository")
 */
class Action
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
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=255, nullable=true)
     */
    private $role;

    /**
     * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\FonctionPermission", mappedBy="action")
     * @ORM\JoinColumn(nullable=true)
     */
    private $fonctionPermissions;

    /**
     * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\UserNotification", mappedBy="action")
     * @ORM\JoinColumn(nullable=true)
     */
    private $userNotifications;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fonctionPermissions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userNotifications = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Action
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
     * @return Action
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
     * Set role
     *
     * @param string $role
     *
     * @return Action
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
     * Add fonctionPermission
     *
     * @param \Nurun\Bundle\RhBundle\Entity\FonctionPermission $fonctionPermission
     *
     * @return Action
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

    /**
     * Add userNotification
     *
     * @param \Nurun\Bundle\RhBundle\Entity\UserNotification $userNotification
     *
     * @return Action
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
}
