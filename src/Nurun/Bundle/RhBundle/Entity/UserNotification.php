<?php

namespace Nurun\Bundle\RhBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserNotification
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nurun\Bundle\RhBundle\Entity\UserNotificationRepository")
 */
class UserNotification
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
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\UserBundle\Entity\User", inversedBy="userNotifications")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Nurun\Bundle\RhBundle\Entity\Action", inversedBy="userNotifications")
     */
    private $action; 


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
     * Set user
     *
     * @param \Nurun\Bundle\UserBundle\Entity\User $user
     *
     * @return UserNotification
     */
    public function setUser(\Nurun\Bundle\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        if($user != null){
            $user->addUserNotification($this);
        }

        return $this;
    }

    /**
     * Get user
     *
     * @return \Nurun\Bundle\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set action
     *
     * @param \Nurun\Bundle\RhBundle\Entity\Action $action
     *
     * @return UserNotification
     */
    public function setAction(\Nurun\Bundle\RhBundle\Entity\Action $action = null)
    {
        $this->action = $action;

        if($action != null){
            $action->addUserNotification($this);
        }

        return $this;
    }

    /**
     * Get action
     *
     * @return \Nurun\Bundle\RhBundle\Entity\Action
     */
    public function getAction()
    {
        return $this->action;
    }
}
