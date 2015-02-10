<?php

namespace Desport\PanelBundle\Entity\EventType;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientRecord
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ClientRecord extends \Desport\PanelBundle\Entity\Event
{
    protected $id;
    
    protected $date;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Desport\PanelBundle\Entity\Client")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    protected $client;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Desport\PanelBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
    /**
     * @var text $text
     *
     * @ORM\Column(name="text", type="text")
     */
    protected $text;


    public function getType()
    {
        return 'ClientRecord';
    }
    
    /**
     * Set client
     *
     * @param boolean $client
     * @return ClientRecord
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
     * @return UserCreated
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
     * Set text
     *
     * @param string $text
     * @return UserCreated
     */
    public function setText($text)
    {
        $text = str_replace(array("<br>","<br />"),"\n",str_replace("\n",'',$text));
        $this->text = $text;
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
}
