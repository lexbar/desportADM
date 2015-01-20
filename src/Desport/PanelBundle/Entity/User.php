<?php

namespace Desport\PanelBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="fos_user")
 * @ORM\Entity
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="string", length=255)
     */
    private $avatar = '';
    
    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="users")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;
    
    /**
     * @ORM\OneToMany(targetEntity="Invoice", mappedBy="user")
     */
    private $invoices;
    
    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="user")
     */
    private $events;
    
    /**
     * @ORM\OneToMany(targetEntity="Site", mappedBy="userCreated")
     */
    private $sites;
    
    /**
     * @ORM\OneToMany(targetEntity="Transaction", mappedBy="user")
     */
     private $transactions;


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
     * Set client
     *
     * @param \Desport\PanelBundle\Entity\Client $client
     * @return User
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
     * Add invoices
     *
     * @param \Desport\PanelBundle\Entity\Invoice $invoices
     * @return User
     */
    public function addInvoice(\Desport\PanelBundle\Entity\Invoice $invoices)
    {
        $this->invoices[] = $invoices;

        return $this;
    }

    /**
     * Remove invoices
     *
     * @param \Desport\PanelBundle\Entity\Invoice $invoices
     */
    public function removeInvoice(\Desport\PanelBundle\Entity\Invoice $invoices)
    {
        $this->invoices->removeElement($invoices);
    }

    /**
     * Get invoices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * Add events
     *
     * @param \Desport\PanelBundle\Entity\Event $events
     * @return User
     */
    public function addEvent(\Desport\PanelBundle\Entity\Event $events)
    {
        $this->events[] = $events;

        return $this;
    }

    /**
     * Remove events
     *
     * @param \Desport\PanelBundle\Entity\Event $events
     */
    public function removeEvent(\Desport\PanelBundle\Entity\Event $events)
    {
        $this->events->removeElement($events);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Add sites
     *
     * @param \Desport\PanelBundle\Entity\Site $sites
     * @return User
     */
    public function addSite(\Desport\PanelBundle\Entity\Site $sites)
    {
        $this->sites[] = $sites;

        return $this;
    }

    /**
     * Remove sites
     *
     * @param \Desport\PanelBundle\Entity\Site $sites
     */
    public function removeSite(\Desport\PanelBundle\Entity\Site $sites)
    {
        $this->sites->removeElement($sites);
    }

    /**
     * Get sites
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSites()
    {
        return $this->sites;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->invoices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sites = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Set name
     *
     * @param string $name
     * @return User
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
     * Set avatar
     *
     * @param string $avatar
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string 
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Add transactions
     *
     * @param \Desport\PanelBundle\Entity\Transaction $transactions
     * @return User
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
}
