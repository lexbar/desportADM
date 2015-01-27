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
     * @ORM\ManyToOne(targetEntity="\Desport\PanelBundle\Entity\Event")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;


    public function getType()
    {
        return 'ClientCreated';
    }
    
    /**
     * Set user
     *
     * @param boolean $user
     * @return ClientCreated
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
