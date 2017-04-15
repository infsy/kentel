<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PrioriteBesoin
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\PrioriteBesoinRepository")
 */
class PrioriteBesoin
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
     * @ORM\Column(name="priorite", type="string", length=255, unique=true, nullable=false)
     */
    private $priorite;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", unique=false, nullable=true)
     */
    private $description;

    public function __toString()
    {
        return ($this->getPriorite());
    }

    public function getDisplay()
    {
        return ($this->getPriorite());
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
     * Set priorite
     *
     * @param string $priorite
     *
     * @return PrioriteDemande
     */
    public function setPriorite($priorite)
    {
        $this->priorite = $priorite;

        return $this;
    }

    /**
     * Get priorite
     *
     * @return string
     */
    public function getPriorite()
    {
        return $this->priorite;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return PrioriteBesoin
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
}
