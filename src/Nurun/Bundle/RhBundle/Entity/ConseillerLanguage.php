<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConseillerLanguage
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\ConseillerLanguageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ConseillerLanguage
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
    * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Conseiller", inversedBy="languages")
    * @ORM\JoinColumn(nullable=false)
    */
      private $conseiller;
      
  /**
   * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Language", inversedBy="conseillers")
   * @ORM\JoinColumn(nullable=false)
   */
      private $language;

    /**
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Niveau")
     * @ORM\JoinColumn(nullable=false)
     */
    private $niveau;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="date", nullable=false)
     */
    private $created;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="date", nullable=true)
     */
    private $updated;

    /**
     *
     * @ORM\PrePersist
     */
    public function updateCreated()
    {
        $this->setCreated(new \DateTime('now'));

        if ($this->getUpdated() == null) {
            $this->setUpdated(new \DateTime('now'));
        }
    }

     /**
     *
     * @ORM\PreUpdate
     */
    public function updateUpdated()
    {
        $this->setUpdated(new \DateTime('now'));
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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return ConseillerLanguage
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return ConseillerLanguage
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set conseiller
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Conseiller $conseiller
     *
     * @return ConseillerLanguage
     */
    public function setConseiller(\Nurun\Bundle\RhBundle\Entity\Conseiller $conseiller)
    {
        $this->conseiller = $conseiller;

        return $this;
    }

    /**
     * Get conseiller
     *
     * @return \Nurun\Bundle\RhBundle\Entity\Conseiller
     */
    public function getConseiller()
    {
        return $this->conseiller;
    }

    /**
     * Set language
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Language $language
     *
     * @return ConseillerLanguage
     */
    public function setLanguage(\Nurun\Bundle\RhBundle\Entity\Language $language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return \Nurun\Bundle\RhBundle\Entity\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set niveau
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Niveau $niveau
     *
     * @return ConseillerLanguage
     */
    public function setNiveau(\Nurun\Bundle\RhBundle\Entity\Niveau $niveau)
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * Get niveau
     *
     * @return \Nurun\Bundle\RhBundle\Entity\Niveau
     */
    public function getNiveau()
    {
        return $this->niveau;
    }
}
