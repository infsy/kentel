<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConseillerFonction
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\ConseillerFonctionRepository")
 */
class ConseillerFonction
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
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Conseiller", inversedBy="conseillerFonctions")
     */
    private $conseiller;

    /**
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Fonction", inversedBy="conseillerFonctions")
     */
    private $fonction;    

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
     * Set conseiller
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Conseiller $conseiller
     *
     * @return ConseillerFonction
     */
    public function setConseiller(\Nurun\Bundle\RhBundle\Entity\Conseiller $conseiller = null)
    {
        $this->conseiller = $conseiller;

        if($conseiller != null){
            $conseiller->addConseillerFonction($this);
        }

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
     * Set fonction
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Fonction $fonction
     *
     * @return ConseillerFonction
     */
    public function setFonction(\Nurun\Bundle\RhBundle\Entity\Fonction $fonction = null)
    {
        $this->fonction = $fonction;

        if($fonction != null){
            $fonction->addConseillerFonction($this);
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
}
