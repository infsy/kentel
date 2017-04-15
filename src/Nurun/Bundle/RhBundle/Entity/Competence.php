<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Competence
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\CompetenceRepository")
 */
class Competence
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
     * @ORM\Column(name="description", type="string", length=40, unique=false, nullable=true)
     */
    private $competence;

    /**
     * @var string
     *
     * @ORM\Column(name="descriptionlongue", type="string", length=255, unique=true, nullable=true)
     */
    private $description;

    /**
    * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\ConseillerCompetence", mappedBy="competence", cascade={"persist"})
    * @ORM\JoinColumn(nullable=true)
    */
    private $conseillers;

    /**
    * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\TypeCompetence",cascade={"persist"})
    * @ORM\JoinColumn(nullable=true)
     */
    private $type;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    public function __toString() {
        return ($this->getCompetence());
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
     * Set competence
     *
     * @param string $competence
     *
     * @return Competence
     */
    public function setCompetence($competence)
    {
        $this->competence = $competence;

        return $this;
    }

    /**
     * Get competence
     *
     * @return string
     */
    public function getCompetence()
    {
        return $this->competence;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Competence
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
     * Add conseiller
     *
     * @param \Nurun\Bundle\RhBundle\Entity\ConseillerCompetence $conseiller
     *
     * @return Competence
     */
    public function addConseiller(\Nurun\Bundle\RhBundle\Entity\ConseillerCompetence $conseiller)
    {
        $this->conseillers[] = $conseiller;

        return $this;
    }

    /**
     * Remove conseiller
     *
     * @param \Nurun\Bundle\RhBundle\Entity\ConseillerCompetence $conseiller
     */
    public function removeConseiller(\Nurun\Bundle\RhBundle\Entity\ConseillerCompetence $conseiller)
    {
        $this->conseillers->removeElement($conseiller);
    }

    /**
     * Get conseillers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConseillers()
    {
        return $this->conseillers;
    }

    /**
     * Set type
     *
     * @param \Nurun\Bundle\RhBundle\Entity\TypeCompetence $type
     *
     * @return Competence
     */
    public function setType(\Nurun\Bundle\RhBundle\Entity\TypeCompetence $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \Nurun\Bundle\RhBundle\Entity\TypeCompetence
     */
    public function getType()
    {
        return $this->type;
    }
}
