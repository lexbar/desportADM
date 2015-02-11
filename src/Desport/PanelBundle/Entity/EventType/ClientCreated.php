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
    
    protected $client;
    
    protected $user;


    public function getType()
    {
        return 'ClientCreated';
    }
}
