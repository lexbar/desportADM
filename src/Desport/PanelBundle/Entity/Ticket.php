<?php

namespace Desport\PanelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ticket
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\Column(name="stateDate", type="datetime", nullable=true)
     */
    private $stateDate;

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
     * Constructor
     */
    public function __construct()
    {
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set stateDate
     *
     * @param \DateTime $stateDate
     * @return Ticket
     */
    public function setStateDate($stateDate)
    {
        $this->stateDate = $stateDate;

        return $this;
    }

    /**
     * Get stateDate
     *
     * @return \DateTime 
     */
    public function getStateDate()
    {
        return $this->stateDate;
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
