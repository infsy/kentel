<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * VicePresidence
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\VicePresidenceRepository")
 */
class VicePresidence 
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
     * @ORM\Column(name="description", type="string", length=150, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="acronyme", type="string", length=10, nullable=false)
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

    /**
     * Set acronyme
     *
     * @param string $acronyme
     * @return VicePresidence
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
    
    public function __toString() {
        return ($this->getAcronyme());
    }
    
    public function getDisplay() {
        return ($this->getAcronyme());
    }
}
