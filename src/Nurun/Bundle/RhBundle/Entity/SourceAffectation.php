<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SourceAffectation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\SourceAffectationRepository")
 */
class SourceAffectation
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
     * @ORM\Column(name="source", type="string", length=255, unique=true, nullable=false)
     */
    private $source;



    public function __toString()
    {
        return ($this->getSource());
    }

    public function getDisplay()
    {
        return ($this->getSource());
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
     * Set source
     *
     * @param string $source
     *
     * @return SourceAffectation
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }
}
