<?php

namespace Desport\PanelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table()
 * @ORM\Entity
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="Ticket", inversedBy="events")
     * @ORM\JoinColumn(name="ticket_id", referencedColumnName="id")
     */
    private $ticket;

    /**
     * @ORM\ManyToOne(targetEntity="Invoice", inversedBy="events")
     * @ORM\JoinColumn(name="invoice_id", referencedColumnName="id")
     */
    private $invoice;

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="events")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="events")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=255)
     */
    private $tag;


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
     * Set text
     *
     * @param string $text
     * @return Event
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
     * Set ticketLink
     *
     * @param boolean $ticketLink
     * @return Event
     */
    public function setTicketLink($ticketLink)
    {
        $this->ticketLink = $ticketLink;

        return $this;
    }

    /**
     * Get ticketLink
     *
     * @return boolean 
     */
    public function getTicketLink()
    {
        return $this->ticketLink;
    }

    /**
     * Set invoiceLink
     *
     * @param boolean $invoiceLink
     * @return Event
     */
    public function setInvoiceLink($invoiceLink)
    {
        $this->invoiceLink = $invoiceLink;

        return $this;
    }

    /**
     * Get invoiceLink
     *
     * @return boolean 
     */
    public function getInvoiceLink()
    {
        return $this->invoiceLink;
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

    /**
     * Set tag
     *
     * @param string $tag
     * @return Event
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string 
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set ticket
     *
     * @param \Desport\PanelBundle\Entity\Ticket $ticket
     * @return Event
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

    /**
     * Set invoice
     *
     * @param \Desport\PanelBundle\Entity\Invoice $invoice
     * @return Event
     */
    public function setInvoice(\Desport\PanelBundle\Entity\Invoice $invoice = null)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice
     *
     * @return \Desport\PanelBundle\Entity\Invoice 
     */
    public function getInvoice()
    {
        return $this->invoice;
    }
}
