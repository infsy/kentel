<?php

namespace Nurun\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Permissions
 *
 * @ORM\Table(name="user_permissions")
 * @ORM\Entity(repositoryClass="Nurun\Bundle\UserBundle\Entity\PermissionsRepository")
 */
class Permissions
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
     * @var boolean
     *
     * @ORM\Column(name="changePhoto", type="boolean")
     */
    private $changePhoto;

    /**
     * Permissions constructor.
     */
    public function __construct()
    {
        $this->setChangePhoto(false);
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
     * Set changePhoto
     *
     * @param boolean $changePhoto
     *
     * @return Permissions
     */
    public function setChangePhoto($changePhoto)
    {
        $this->changePhoto = $changePhoto;

        return $this;
    }

    /**
     * Get changePhoto
     *
     * @return boolean
     */
    public function getChangePhoto()
    {
        return $this->changePhoto;
    }
}
