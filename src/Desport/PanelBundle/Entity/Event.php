<?php

namespace Desport\PanelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"PlainText" = "\Desport\PanelBundle\Entity\EventType\PlainText","ClientCreated" = "\Desport\PanelBundle\Entity\EventType\ClientCreated","SiteCreated" = "\Desport\PanelBundle\Entity\EventType\SiteCreated","TicketCreated" = "\Desport\PanelBundle\Entity\EventType\TicketCreated","DroppedMessage" = "\Desport\PanelBundle\Entity\EventType\DroppedMessage", "MessageTransfered" = "\Desport\PanelBundle\Entity\EventType\MessageTransfered", "ClientRecord" = "\Desport\PanelBundle\Entity\EventType\ClientRecord"})
 */
class Event
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
     * @ORM\ManyToOne(targetEntity="\Desport\PanelBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Desport\PanelBundle\Entity\Client")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    protected $client;
    
    protected $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


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
     * @return Event
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
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->setDate(new \DateTime('now'));
    }
    
    /**
     * Set client
     *
     * @param boolean $client
     * @return Event
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
     * @return Event
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
}
