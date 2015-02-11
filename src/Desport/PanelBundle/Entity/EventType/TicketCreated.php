<?php

namespace Desport\PanelBundle\Entity\EventType;

use Doctrine\ORM\Mapping as ORM;

/**
 * TicketCreated
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class TicketCreated extends \Desport\PanelBundle\Entity\Event
{
    protected $id;
    
    protected $date;
    
    protected $user;
    
    protected $client;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Desport\PanelBundle\Entity\Ticket")
     * @ORM\JoinColumn(name="ticket_id", referencedColumnName="id")
     */
    protected $ticket;


    public function getType()
    {
        return 'TicketCreated';
    }
    
    /**
     * Set ticket
     *
     * @param boolean $ticket
     * @return TicketCreated
     */
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Get ticket
     *
     * @return boolean 
     */
    public function getTicket()
    {
        return $this->ticket;
    }
}
