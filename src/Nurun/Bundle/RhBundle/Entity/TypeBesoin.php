<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TypeBesoin
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\TypeBesoinRepository")
 */
class TypeBesoin
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
     * @ORM\Column(name="type", type="string", length=255, unique=true, nullable=false)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\BesoinAffectation", mappedBy="typeBesoin")
     */
    private $besoinsAffectation;


    public function __toString()
    {
        return ($this->getType());
    }

    public function getDisplay()
    {
        return ($this->getType());
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
     * Set type
     *
     * @param string $type
     *
     * @return TypeCompetence
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->besoinsAffectation = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add besoinsAffectation
     *
     * @param \Nurun\Bundle\RhBundle\Entity\BesoinAffectation $besoinsAffectation
     *
     * @return TypeBesoin
     */
    public function addBesoinsAffectation(\Nurun\Bundle\RhBundle\Entity\BesoinAffectation $besoinsAffectation)
    {
        $this->besoinsAffectation[] = $besoinsAffectation;

        return $this;
    }

    /**
     * Remove besoinsAffectation
     *
     * @param \Nurun\Bundle\RhBundle\Entity\BesoinAffectation $besoinsAffectation
     */
    public function removeBesoinsAffectation(\Nurun\Bundle\RhBundle\Entity\BesoinAffectation $besoinsAffectation)
    {
        $this->besoinsAffectation->removeElement($besoinsAffectation);
    }

    /**
     * Get besoinsAffectation
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBesoinsAffectation()
    {
        return $this->besoinsAffectation;
    }
}
