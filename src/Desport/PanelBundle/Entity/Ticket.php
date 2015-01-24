<?php

namespace Desport\PanelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ticket
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Ticket
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=30)
     */
    private $state; // new / open / pending reminder / pending auto close- / pending auto close+ / closed successful / closed unsuccessful
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="pending", type="datetime")
     */
    private $pending;

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="tickets")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tickets")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $responsible;

    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="ticket")
     */
    private $events;
    
    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="ticket")
     */
    private $messages;


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
     * Set date
     *
     * @param \DateTime $date
     * @return Ticket
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
     * Set responseTo
     *
     * @param boolean $responseTo
     * @return Ticket
     */
    public function setResponseTo($responseTo)
    {
        $this->responseTo = $responseTo;

        return $this;
    }

    /**
     * Get responseTo
     *
     * @return boolean 
     */
    public function getResponseTo()
    {
        return $this->responseTo;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return Ticket
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
     * Set text
     *
     * @param string $text
     * @return Ticket
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
     * Set state
     *
     * @param string $state
     * @return Ticket
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set client
     *
     * @param boolean $client
     * @return Ticket
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
     * Set user
     *
     * @param boolean $user
     * @return Ticket
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
     * Set event
     *
     * @param boolean $event
     * @return Ticket
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return boolean 
     */
    public function getEvent()
    {
        return $this->event;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add events
     *
     * @param \Desport\PanelBundle\Entity\Event $events
     * @return Ticket
     */
    public function addEvent(\Desport\PanelBundle\Entity\Event $events)
    {
        $this->events[] = $events;

        return $this;
    }

    /**
     * Remove events
     *
     * @param \Desport\PanelBundle\Entity\Event $events
     */
    public function removeEvent(\Desport\PanelBundle\Entity\Event $events)
    {
        $this->events->removeElement($events);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Set pending
     *
     * @param \DateTime $pending
     * @return Ticket
     */
    public function setPending($pending)
    {
        $this->pending = $pending;

        return $this;
    }

    /**
     * Get pending
     *
     * @return \DateTime 
     */
    public function getPending()
    {
        return $this->pending;
    }

    /**
     * Set responsible
     *
     * @param \Desport\PanelBundle\Entity\User $responsible
     * @return Ticket
     */
    public function setResponsible(\Desport\PanelBundle\Entity\User $responsible = null)
    {
        $this->responsible = $responsible;

        return $this;
    }

    /**
     * Get responsible
     *
     * @return \Desport\PanelBundle\Entity\User 
     */
    public function getResponsible()
    {
        return $this->responsible;
    }

    /**
     * Set message
     *
     * @param \Desport\PanelBundle\Entity\Message $message
     * @return Ticket
     */
    public function setMessage(\Desport\PanelBundle\Entity\Message $message = null)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return \Desport\PanelBundle\Entity\Message 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Add messages
     *
     * @param \Desport\PanelBundle\Entity\Message $messages
     * @return Ticket
     */
    public function addMessage(\Desport\PanelBundle\Entity\Message $messages)
    {
        $this->messages[] = $messages;

        return $this;
    }

    /**
     * Remove messages
     *
     * @param \Desport\PanelBundle\Entity\Message $messages
     */
    public function removeMessage(\Desport\PanelBundle\Entity\Message $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
