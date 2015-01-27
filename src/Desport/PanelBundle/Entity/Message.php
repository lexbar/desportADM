<?php

namespace Desport\PanelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Message
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
     * @ORM\Column(name="subject", type="string", length=255)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="emailFrom", type="string", length=255)
     */
    private $emailFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="emailTo", type="string", length=255)
     */
    private $emailTo;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="textHTML", type="text")
     */
    private $textHTML;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="messagesSent")
     * @ORM\JoinColumn(name="user_from_id", referencedColumnName="id")
     */
    private $userFrom;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="messagesReceived")
     * @ORM\JoinColumn(name="user_to_id", referencedColumnName="id")
     */
    private $userTo;

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="messages")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="isRead", type="boolean")
     */
    private $isRead=false;
    
    /**
     * @ORM\OneToMany(targetEntity="MessageAttachment", mappedBy="message")
     */
    private $attachments;
    
    /**
     * @var string
     *
     * @ORM\Column(name="mailgunId", type="string", length=255)
     */
    private $mailgunId='';
    
    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="responses")
     * @ORM\JoinColumn(name="parent_message_id", referencedColumnName="id")
     */
    private $parentMessage;
    
    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="parentMessage")
     */
    private $responses;
    
    /**
     * @ORM\ManyToOne(targetEntity="Ticket", inversedBy="messages")
     * @ORM\JoinColumn(name="ticket_id", referencedColumnName="id")
     */
    private $ticket;
    

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
     * Set subject
     *
     * @param string $subject
     * @return Message
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set emailFrom
     *
     * @param string $emailFrom
     * @return Message
     */
    public function setEmailFrom($emailFrom)
    {
        $this->emailFrom = $emailFrom;

        return $this;
    }

    /**
     * Get emailFrom
     *
     * @return string 
     */
    public function getEmailFrom()
    {
        return $this->emailFrom;
    }

    /**
     * Set emailTo
     *
     * @param string $emailTo
     * @return Message
     */
    public function setEmailTo($emailTo)
    {
        $this->emailTo = $emailTo;

        return $this;
    }

    /**
     * Get emailTo
     *
     * @return string 
     */
    public function getEmailTo()
    {
        return $this->emailTo;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Message
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set textHTML
     *
     * @param string $textHTML
     * @return Message
     */
    public function setTextHTML($textHTML)
    {
        $this->textHTML = $textHTML;

        return $this;
    }

    /**
     * Get textHTML
     *
     * @return string 
     */
    public function getTextHTML()
    {
        return $this->textHTML;
    }

    /**
     * Set userFrom
     *
     * @param boolean $userFrom
     * @return Message
     */
    public function setUserFrom($userFrom)
    {
        $this->userFrom = $userFrom;

        return $this;
    }

    /**
     * Get userFrom
     *
     * @return boolean 
     */
    public function getUserFrom()
    {
        return $this->userFrom;
    }

    /**
     * Set userTo
     *
     * @param boolean $userTo
     * @return Message
     */
    public function setUserTo($userTo)
    {
        $this->userTo = $userTo;

        return $this;
    }

    /**
     * Get userTo
     *
     * @return boolean 
     */
    public function getUserTo()
    {
        return $this->userTo;
    }

    /**
     * Set client
     *
     * @param boolean $client
     * @return Message
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return boolean 
     */
    public function getClient()
    {
        return $this->client;
    }
    
    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->setDate(new \DateTime('now'));
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Message
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Add attachments
     *
     * @param \Desport\PanelBundle\Entity\MessageAttachment $attachments
     * @return Message
     */
    public function addAttachment(\Desport\PanelBundle\Entity\MessageAttachment $attachments)
    {
        $this->attachments[] = $attachments;

        return $this;
    }

    /**
     * Remove attachments
     *
     * @param \Desport\PanelBundle\Entity\MessageAttachment $attachments
     */
    public function removeAttachment(\Desport\PanelBundle\Entity\MessageAttachment $attachments)
    {
        $this->attachments->removeElement($attachments);
    }

    /**
     * Get attachments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttachments()
    {
        return $this->attachments;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attachments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set isRead
     *
     * @param boolean $isRead
     * @return Message
     */
    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;

        return $this;
    }

    /**
     * Get isRead
     *
     * @return boolean 
     */
    public function getIsRead()
    {
        return $this->isRead;
    }

    /**
     * Set mailgunId
     *
     * @param string $mailgunId
     * @return Message
     */
    public function setMailgunId($mailgunId)
    {
        $this->mailgunId = $mailgunId;

        return $this;
    }

    /**
     * Get mailgunId
     *
     * @return string 
     */
    public function getMailgunId()
    {
        return $this->mailgunId;
    }

    /**
     * Set parentMessage
     *
     * @param \Desport\PanelBundle\Entity\Message $parentMessage
     * @return Message
     */
    public function setParentMessage(\Desport\PanelBundle\Entity\Message $parentMessage = null)
    {
        $this->parentMessage = $parentMessage;

        return $this;
    }

    /**
     * Get parentMessage
     *
     * @return \Desport\PanelBundle\Entity\Message 
     */
    public function getParentMessage()
    {
        return $this->parentMessage;
    }

    /**
     * Add responses
     *
     * @param \Desport\PanelBundle\Entity\Message $responses
     * @return Message
     */
    public function addResponse(\Desport\PanelBundle\Entity\Message $responses)
    {
        $this->responses[] = $responses;

        return $this;
    }

    /**
     * Remove responses
     *
     * @param \Desport\PanelBundle\Entity\Message $responses
     */
    public function removeResponse(\Desport\PanelBundle\Entity\Message $responses)
    {
        $this->responses->removeElement($responses);
    }

    /**
     * Get responses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * Set ticket
     *
     * @param \Desport\PanelBundle\Entity\Ticket $ticket
     * @return Message
     */
    public function setTicket(\Desport\PanelBundle\Entity\Ticket $ticket = null)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Get ticket
     *
     * @return \Desport\PanelBundle\Entity\Ticket 
     */
    public function getTicket()
    {
        return $this->ticket;
    }
    
    public function recipientDomain($domain = false)
    {
        $emailtoClean = str_replace(array('<','>'), '', $this->getEmailTo());
        $address = explode('@', $emailtoClean) ;
        
        if(count($address) < 2)
        {
            return false;
        }
        
        if($domain)
        {
            return trim($address[1]) == trim($domain);
        }
        else
        {
            return trim($address[1]);
        }
        
    }
    
    public function getSwiftEmailFrom()
    {
        $addr = $this->getEmailFrom();
        
        if(preg_match("/(.+)\<(.+)\>/", trim($addr), $matches)) // catch the format "Name <email@address>"
        {
            return array($matches[2] => $matches[1]);
        }
        
        return $addr;
    }
    
    public function getSwiftEmailTo()
    {
        $addr = $this->getEmailTo();
        
        if(preg_match("/(.+)\<(.+)\>/", trim($addr), $matches)) // catch the format "Name <email@address>"
        {
            return array($matches[2] => $matches[1]);
        }
        
        return $addr;
    }
}
