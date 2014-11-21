<?php

namespace Desport\PanelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Client
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Client
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
     * @var string
     *
     * @ORM\Column(name="contact_name", type="string", length=255)
     */
    private $contactName;

    /**
     * @var string
     *
     * @ORM\Column(name="address_country", type="string", length=25)
     */
    private $addressCountry;

    /**
     * @var string
     *
     * @ORM\Column(name="address_state", type="string", length=100)
     */
    private $addressState;

    /**
     * @var string
     *
     * @ORM\Column(name="address_city", type="string", length=100)
     */
    private $addressCity;

    /**
     * @var string
     *
     * @ORM\Column(name="address_zip", type="string", length=30)
     */
    private $addressZip;

    /**
     * @var string
     *
     * @ORM\Column(name="address_address", type="string", length=255)
     */
    private $addressAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255)
     */
    private $website;

    /**
     * @var string
     *
     * @ORM\Column(name="comments", type="text")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="Site", mappedBy="client")
     */
    private $sites;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="client")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="Invoice", mappedBy="client")
     */
    private $invoices;

    /**
     * @ORM\OneToMany(targetEntity="Ticket", mappedBy="client")
     */
    private $tickets;

    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="client")
     */
    private $events;


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
     * @return Client
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
     * Set contactName
     *
     * @param string $contactName
     * @return Client
     */
    public function setContactName($contactName)
    {
        $this->contactName = $contactName;

        return $this;
    }

    /**
     * Get contactName
     *
     * @return string 
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * Set addressCountry
     *
     * @param string $addressCountry
     * @return Client
     */
    public function setAddressCountry($addressCountry)
    {
        $this->addressCountry = $addressCountry;

        return $this;
    }

    /**
     * Get addressCountry
     *
     * @return string 
     */
    public function getAddressCountry()
    {
        return $this->addressCountry;
    }

    /**
     * Set addressState
     *
     * @param string $addressState
     * @return Client
     */
    public function setAddressState($addressState)
    {
        $this->addressState = $addressState;

        return $this;
    }

    /**
     * Get addressState
     *
     * @return string 
     */
    public function getAddressState()
    {
        return $this->addressState;
    }

    /**
     * Set addressCity
     *
     * @param string $addressCity
     * @return Client
     */
    public function setAddressCity($addressCity)
    {
        $this->addressCity = $addressCity;

        return $this;
    }

    /**
     * Get addressCity
     *
     * @return string 
     */
    public function getAddressCity()
    {
        return $this->addressCity;
    }

    /**
     * Set addressZip
     *
     * @param string $addressZip
     * @return Client
     */
    public function setAddressZip($addressZip)
    {
        $this->addressZip = $addressZip;

        return $this;
    }

    /**
     * Get addressZip
     *
     * @return string 
     */
    public function getAddressZip()
    {
        return $this->addressZip;
    }

    /**
     * Set addressAddress
     *
     * @param string $addressAddress
     * @return Client
     */
    public function setAddressAddress($addressAddress)
    {
        $this->addressAddress = $addressAddress;

        return $this;
    }

    /**
     * Get addressAddress
     *
     * @return string 
     */
    public function getAddressAddress()
    {
        return $this->addressAddress;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Client
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Client
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return Client
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set comments
     *
     * @param string $comments
     * @return Client
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return string 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set site
     *
     * @param boolean $site
     * @return Client
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
     * @return Client
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
     * Set invoices
     *
     * @param boolean $invoices
     * @return Client
     */
    public function setInvoices($invoices)
    {
        $this->invoices = $invoices;

        return $this;
    }

    /**
     * Get invoices
     *
     * @return boolean 
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * Set tickets
     *
     * @param boolean $tickets
     * @return Client
     */
    public function setTickets($tickets)
    {
        $this->tickets = $tickets;

        return $this;
    }

    /**
     * Get tickets
     *
     * @return boolean 
     */
    public function getTickets()
    {
        return $this->tickets;
    }

    /**
     * Set events
     *
     * @param boolean $events
     * @return Client
     */
    public function setEvents($events)
    {
        $this->events = $events;

        return $this;
    }

    /**
     * Get events
     *
     * @return boolean 
     */
    public function getEvents()
    {
        return $this->events;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->invoices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tickets = new \Doctrine\Common\Collections\ArrayCollection();
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add sites
     *
     * @param \Desport\PanelBundle\Entity\Site $sites
     * @return Client
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
     * Add users
     *
     * @param \Desport\PanelBundle\Entity\User $users
     * @return Client
     */
    public function addUser(\Desport\PanelBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Desport\PanelBundle\Entity\User $users
     */
    public function removeUser(\Desport\PanelBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add invoices
     *
     * @param \Desport\PanelBundle\Entity\Invoice $invoices
     * @return Client
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
     * Add tickets
     *
     * @param \Desport\PanelBundle\Entity\Ticket $tickets
     * @return Client
     */
    public function addTicket(\Desport\PanelBundle\Entity\Ticket $tickets)
    {
        $this->tickets[] = $tickets;

        return $this;
    }

    /**
     * Remove tickets
     *
     * @param \Desport\PanelBundle\Entity\Ticket $tickets
     */
    public function removeTicket(\Desport\PanelBundle\Entity\Ticket $tickets)
    {
        $this->tickets->removeElement($tickets);
    }

    /**
     * Add events
     *
     * @param \Desport\PanelBundle\Entity\Event $events
     * @return Client
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
}
