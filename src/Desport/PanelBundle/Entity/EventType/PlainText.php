<?php

namespace Desport\PanelBundle\Entity\EventType;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlainText
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class PlainText extends \Desport\PanelBundle\Entity\Event
{
    protected $id;
    
    protected $date;
    
    /**
     * @var text $text
     *
     * @ORM\Column(name="text", type="text")
     */
    protected $text;


    public function getType()
    {
        return 'PlainText';
    }
    
    /**
     * Set text
     *
     * @param string $text
     * @return PlainText
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
