<?php

namespace Desport\PanelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Site
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Site
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    private $dateCreated;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="sites")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userCreated;

    /**
     * @var integer
     *
     * @ORM\Column(name="bandwidth", type="integer")
     */
    private $bandwidth;

    /**
     * @var integer
     *
     * @ORM\Column(name="quota", type="integer")
     */
    private $quota;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_filespace", type="integer")
     */
    private $maxFilespace;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_activeusers", type="integer")
     */
    private $maxActiveusers;

    /**
     * @var boolean
     *
     * @ORM\Column(name="ads", type="boolean")
     */
    private $ads;

    /**
     * @var array
     *
     * @ORM\Column(name="bundles", type="array")
     */
    private $bundles;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expires", type="datetime")
     */
    private $expires;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;
    
    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="sites")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;

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
     * Set name
     *
     * @param string $name
     * @return Site
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Site
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set userCreated
     *
     * @param string $userCreated
     * @return Site
     */
    public function setUserCreated($userCreated)
    {
        $this->userCreated = $userCreated;

        return $this;
    }

    /**
     * Get userCreated
     *
     * @return string 
     */
    public function getUserCreated()
    {
        return $this->userCreated;
    }

    /**
     * Set bandwidth
     *
     * @param integer $bandwidth
     * @return Site
     */
    public function setBandwidth($bandwidth)
    {
        $this->bandwidth = $bandwidth;

        return $this;
    }

    /**
     * Get bandwidth
     *
     * @return integer 
     */
    public function getBandwidth()
    {
        return $this->bandwidth;
    }

    /**
     * Set quota
     *
     * @param integer $quota
     * @return Site
     */
    public function setQuota($quota)
    {
        $this->quota = $quota;

        return $this;
    }

    /**
     * Get quota
     *
     * @return integer 
     */
    public function getQuota()
    {
        return $this->quota;
    }

    /**
     * Set maxFilespace
     *
     * @param integer $maxFilespace
     * @return Site
     */
    public function setMaxFilespace($maxFilespace)
    {
        $this->maxFilespace = $maxFilespace;

        return $this;
    }

    /**
     * Get maxFilespace
     *
     * @return integer 
     */
    public function getMaxFilespace()
    {
        return $this->maxFilespace;
    }

    /**
     * Set maxActiveusers
     *
     * @param integer $maxActiveusers
     * @return Site
     */
    public function setMaxActiveusers($maxActiveusers)
    {
        $this->maxActiveusers = $maxActiveusers;

        return $this;
    }

    /**
     * Get maxActiveusers
     *
     * @return integer 
     */
    public function getMaxActiveusers()
    {
        return $this->maxActiveusers;
    }

    /**
     * Set ads
     *
     * @param boolean $ads
     * @return Site
     */
    public function setAds($ads)
    {
        $this->ads = $ads;

        return $this;
    }

    /**
     * Get ads
     *
     * @return boolean 
     */
    public function getAds()
    {
        return $this->ads;
    }

    /**
     * Set bundles
     *
     * @param array $bundles
     * @return Site
     */
    public function setBundles($bundles)
    {
        $this->bundles = $bundles;

        return $this;
    }

    /**
     * Get bundles
     *
     * @return array 
     */
    public function getBundles()
    {
        return $this->bundles;
    }

    /**
     * Set expires
     *
     * @param \DateTime $expires
     * @return Site
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;

        return $this;
    }

    /**
     * Get expires
     *
     * @return \DateTime 
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Site
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set client
     *
     * @param \Desport\PanelBundle\Entity\Client $client
     * @return Site
     */
    public function setClient(\Desport\PanelBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \Desport\PanelBundle\Entity\Client 
     */
    public function getClient()
    {
        return $this->client;
    }
}
