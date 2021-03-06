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
    
    protected $client;
    
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
