<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Language
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\LanguageRepository")
 */
class Language
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
     * @ORM\Column(name="language", type="string", length=90, unique=true, nullable=false)
     */
    private $language;

     /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=3, unique=true, nullable=false)
     */
    private $code;

   /**
    * @ORM\OneToMany(targetEntity="Nurun\Bundle\RhBundle\Entity\ConseillerLanguage", mappedBy="language", cascade={"remove", "persist"})
    */
    private $conseillers;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->conseillers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set language
     *
     * @param string $language
     *
     * @return Language
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Language
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Add conseiller
     *
     * @param \Nurun\Bundle\RhBundle\Entity\ConseillerLanguage $conseiller
     *
     * @return Language
     */
    public function addConseiller(\Nurun\Bundle\RhBundle\Entity\ConseillerLanguage $conseiller)
    {
        $this->conseillers[] = $conseiller;

        return $this;
    }

    /**
     * Remove conseiller
     *
     * @param \Nurun\Bundle\RhBundle\Entity\ConseillerLanguage $conseiller
     */
    public function removeConseiller(\Nurun\Bundle\RhBundle\Entity\ConseillerLanguage $conseiller)
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
}
