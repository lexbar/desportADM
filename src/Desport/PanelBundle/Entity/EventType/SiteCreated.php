<?php

namespace Desport\PanelBundle\Entity\EventType;

use Doctrine\ORM\Mapping as ORM;

/**
 * SiteCreated
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SiteCreated extends \Desport\PanelBundle\Entity\Event
{
    protected $id;
    
    protected $date;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Desport\PanelBundle\Entity\Site")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     */
    protected $site;
    
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
        return 'SiteCreated';
    }
    
    /**
     * Set site
     *
     * @param boolean $site
     * @return SiteCreated
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return boolean 
     */
    public function getSite()
    {
        return $this->site;
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
     * @return Place
     */
    public function setText($text)
    {
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
