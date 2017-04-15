<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FonctionPermission
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\FonctionPermissionRepository")
 */
class FonctionPermission
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
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Fonction", inversedBy="fonctionPermissions")
     */
    private $fonction;

    /**
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Action", inversedBy="fonctionPermissions")
     */
    private $action;  


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
     * Set fonction
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Fonction $fonction
     *
     * @return FonctionPermission
     */
    public function setFonction(\Nurun\Bundle\RhBundle\Entity\Fonction $fonction = null)
    {
        $this->fonction = $fonction;

        if($fonction != null){
            $fonction->addFonctionPermission($this);
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

    /**
     * Set action
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Action $action
     *
     * @return FonctionPermission
     */
    public function setAction(\Nurun\Bundle\RhBundle\Entity\Action $action = null)
    {
        $this->action = $action;

        if($action != null){
            $action->addFonctionPermission($this);
        }

        return $this;
    }

    /**
     * Get action
     *
     * @return \Nurun\Bundle\RhBundle\Entity\Action
     */
    public function getAction()
    {
        return $this->action;
    }
}
