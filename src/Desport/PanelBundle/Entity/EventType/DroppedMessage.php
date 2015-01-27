<?php

namespace Desport\PanelBundle\Entity\EventType;

use Doctrine\ORM\Mapping as ORM;

/**
 * DroppedMessage
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class DroppedMessage extends \Desport\PanelBundle\Entity\Event
{
    protected $id;
    
    protected $date;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Desport\PanelBundle\Entity\Message")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id")
     */
    protected $message;
    
    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="string", length=250)
     */
    protected $reason;


    public function getType()
    {
        return 'DroppedMessage';
    }
    
    /**
     * Set message
     *
     * @param boolean $message
     * @return DroppedMessage
     */
    public function setMessage($message)
    {
        $this->message = message;

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
     * Set reason
     *
     * @param string $reason
     * @return DroppedMessage
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return string 
     */
    public function getReason()
    {
        return $this->reason;
    }
}
