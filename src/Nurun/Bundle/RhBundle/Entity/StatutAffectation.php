<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * StatutAffectation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\StatutAffectationRepository")
 */
class StatutAffectation 
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
     * @ORM\Column(name="description", type="string", length=60, nullable=false)
     */
    private $description;
    
    /**
     * @var string
     *
     * @ORM\Column(name="acronyme", type="string", length=8, nullable=false)
     */
    private $acronyme;

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
     * Set description
     *
     * @param string $description
     * @return StatutConseiller
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
    
    public function __toString() {
        return ($this->getAcronyme() . ' - ' . $this->getDescription());
    }
    
    public function getDisplay() {
        return ($this->getAcronyme() . ' - ' . $this->getDescription());
    }

    /**
     * Set acronyme
     *
     * @param string $acronyme
     *
     * @return StatutAffectation
     */
    public function setAcronyme($acronyme)
    {
        $this->acronyme = $acronyme;

        return $this;
    }

    /**
     * Get acronyme
     *
     * @return string
     */
    public function getAcronyme()
    {
        return $this->acronyme;
    }
}
