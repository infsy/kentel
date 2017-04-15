<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Niveau
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\NiveauRepository")
 */
class Niveau
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
     * @ORM\Column(name="niveau", type="string", length=15, unique=false, nullable=false)
     */
    private $niveau;

     /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", unique=false, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="valeur", type="integer", unique=false, nullable=false)
     */
    private $force;

    /**
     * @var string
     *
     * @ORM\Column(name="domaine", type="string", unique=false, nullable=true)
     */
    private $domaine;


    public function __toString()
    {
        return ($this->getNiveau());
    }

    public function getDisplay()
    {
        return ($this->getNiveau());
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
     * Set niveau
     *
     * @param string $niveau
     *
     * @return Niveau
     */
    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * Get niveau
     *
     * @return string
     */
    public function getNiveau()
    {
        return $this->niveau;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Niveau
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
     * Set force
     *
     * @param integer $force
     *
     * @return Niveau
     */
    public function setForce($force)
    {
        $this->force = $force;

        return $this;
    }

    /**
     * Get force
     *
     * @return integer
     */
    public function getForce()
    {
        return $this->force;
    }

    /**
     * Set domaine
     *
     * @param string $domaine
     *
     * @return Niveau
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
}
