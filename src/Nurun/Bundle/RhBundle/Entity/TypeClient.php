<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TypeClient
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\TypeClientRepository")
 */
class TypeClient
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
     * @ORM\oneToMany(targetEntity="Client", mappedBy="typeClient")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $clients;


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
     * Add client
     *
     * @param \Nurun\Bundle\RhBundle\Entity\client $client
     *
     * @return Client
     */
    public function addClient(\Nurun\Bundle\RhBundle\Entity\client $client)
    {
        $this->client[] = $clients;

        return $this;
    }

    /**
     * Remove client
     *
     * @param \Nurun\Bundle\RhBundle\Entity\client $client
     */
    public function removeClient(\Nurun\Bundle\RhBundle\Entity\client $client)
    {
        $this->clients->removeElement($client);
    }

    /**
     * Get client
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClients()
    {
        return $this->clients;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->clients = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
