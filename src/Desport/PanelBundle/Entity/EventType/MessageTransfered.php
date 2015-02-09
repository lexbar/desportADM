<?php

namespace Desport\PanelBundle\Entity\EventType;

use Doctrine\ORM\Mapping as ORM;

/**
 * MessageTransfered
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class MessageTransfered extends \Desport\PanelBundle\Entity\Event
{
    protected $id;
    
    protected $date;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Desport\PanelBundle\Entity\Message")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id")
     */
    protected $message;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Desport\PanelBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Desport\PanelBundle\Entity\User")
     * @ORM\JoinColumn(name="user_transfered_id", referencedColumnName="id")
     */
    protected $user_transfered;


    public function getType()
    {
        return 'MessageTransfered';
    }
    
    /**
     * Set message
     *
     * @param boolean $message
     * @return MessageTransfered
     */
    public function setMessage(\Desport\PanelBundle\Entity\Message $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return boolean 
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     * Set user
     *
     * @param boolean $user
     * @return MessageTransfered
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return boolean 
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Set user
     *
     * @param boolean $user
     * @return MessageTransfered
     */
    public function setUserTransfered($user)
    {
        $this->user_transfered = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return boolean 
     */
    public function getUserTransfered()
    {
        return $this->user_transfered;
    }
}
