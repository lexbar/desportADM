<?php

namespace Desport\PanelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Site
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
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
    private $bandwidth=0;

    /**
     * @var integer
     *
     * @ORM\Column(name="quota", type="integer")
     */
    private $quota=0;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_filespace", type="integer")
     */
    private $maxFilespace=0;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_activeusers", type="integer")
     */
    private $maxActiveusers=0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="ads", type="boolean")
     */
    private $ads=false;

    /**
     * @var array
     *
     * @ORM\Column(name="bundles", type="array")
     */
    private $bundles=array();

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expires", type="datetime", nullable=true)
     */
    private $expires=null;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;
    
    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=30)
     */
    private $state;
    
    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="sites")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;
    
    /**
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="sites")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;
    
    /**
     * @ORM\OneToMany(targetEntity="Transaction", mappedBy="site")
     */
     private $transactions;
     
    /**
     * @ORM\OneToMany(targetEntity="InstallQueue", mappedBy="site")
     */
    private $installQueues;

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
    
    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->setDateCreated(new \DateTime('now'));
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->transactions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add transactions
     *
     * @param \Desport\PanelBundle\Entity\Transaction $transactions
     * @return Site
     */
    public function addTransaction(\Desport\PanelBundle\Entity\Transaction $transactions)
    {
        $this->transactions[] = $transactions;

        return $this;
    }

    /**
     * Remove transactions
     *
     * @param \Desport\PanelBundle\Entity\Transaction $transactions
     */
    public function removeTransaction(\Desport\PanelBundle\Entity\Transaction $transactions)
    {
        $this->transactions->removeElement($transactions);
    }

    /**
     * Get transactions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTransactions()
    {
        return $this->transactions;
    }
    
    public function parseProductProperties(array $properties)
    {
        foreach($properties as $k=>$v)
        {
            switch($k)
            {
                case 'bandwidth':
                    $this->setBandwidth($v);
                break;
                case 'quota':
                    $this->setQuota($v);
                break;
                case 'max_filespace':
                    $this->setMaxFilespace($v);
                break;
                case 'max_activeusers':
                    $this->setMaxActiveusers($v);
                break;
                case 'ads':
                    $this->setAds($v);
                break;
                case 'bundles':
                    $this->setBundles($v);
                break;
                case 'expires':
                    $this->setExpires(new \DateTime($v));
                break;
            }
        }
    }

    /**
     * Set state
     *
     * @param string $state
     * @return Site
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set product
     *
     * @param \Desport\PanelBundle\Entity\Product $product
     * @return Site
     */
    public function setProduct(\Desport\PanelBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \Desport\PanelBundle\Entity\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Add installQueues
     *
     * @param \Desport\PanelBundle\Entity\InstallQueue $installQueues
     * @return Site
     */
    public function addInstallQueue(\Desport\PanelBundle\Entity\InstallQueue $installQueues)
    {
        $this->installQueues[] = $installQueues;

        return $this;
    }

    /**
     * Remove installQueues
     *
     * @param \Desport\PanelBundle\Entity\InstallQueue $installQueues
     */
    public function removeInstallQueue(\Desport\PanelBundle\Entity\InstallQueue $installQueues)
    {
        $this->installQueues->removeElement($installQueues);
    }

    /**
     * Get installQueues
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInstallQueues()
    {
        return $this->installQueues;
    }
}
