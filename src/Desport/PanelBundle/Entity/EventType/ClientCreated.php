<?php

namespace Desport\PanelBundle\Entity\EventType;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientCreated
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ClientCreated extends \Desport\PanelBundle\Entity\Event
{
    protected $id;
    
    protected $date;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Desport\PanelBundle\Entity\Client")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    protected $client;


    public function getType()
    {
        return 'ClientCreated';
    }
    
    /**
     * Set client
     *
     * @param boolean $client
     * @return ClientCreated
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
    public function getUser()
    {
        return $this->client;
    }
}
