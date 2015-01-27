<?php

namespace Desport\PanelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Client
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
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
     * @var string
     *
     * @ORM\Column(name="stage", type="string", length=25)
     */
    private $stage;
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

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
     * @ORM\OneToMany(targetEntity="Message", mappedBy="client")
     */
    private $messages;


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

    /**
     * Set stage
     *
     * @param string $stage
     * @return Client
     */
    public function setStage($stage)
    {
        $this->stage = $stage;

        return $this;
    }

    /**
     * Get stage
     *
     * @return string 
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Client
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }
    
    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->setDate(new \DateTime('now'));
    }
    
    public function getAddressFull()
    {
        $full = array();
        
        if($this->getAddressAddress())
        {
            $full[] = $this->getAddressAddress();
        }
        
        if($this->getAddressZip() || $this->getAddressCity() || $this->getAddressState())
        {
            $full[] = $this->getAddressZip() . ' ' . $this->getAddressCity() . ' ' . $this->getAddressState();
        }
        
        if($this->getAddressCountry())
        {
            $full[] = $this->getCountryByCode($this->getAddressCountry());
        }
        
        return implode(', ', $full);
    }
    
    public function getCountryByCode($code)
    { 
        $countries = array(
          "AL"=>"Albania",
          "DE"=>"Alemania",
          "AD"=>"Andorra",
          "AM"=>"Armenia",
          "AT"=>"Austria",
          "AZ"=>"Azerbaiyán",
          "BE"=>"Bélgica",
          "BY"=>"Bielorrusia",
          "BA"=>"Bosnia y Herzegovina",
          "BG"=>"Bulgaria",
          "CY"=>"Chipre",
          "VA"=>"Ciudad del Vaticano (Santa Sede)",
          "HR"=>"Croacia",
          "DK"=>"Dinamarca",
          "SK"=>"Eslovaquia",
          "SI"=>"Eslovenia",
          "ES"=>"España",
          "EE"=>"Estonia",
          "FI"=>"Finlandia",
          "FR"=>"Francia",
          "GE"=>"Georgia",
          "GR"=>"Grecia",
          "HU"=>"Hungría",
          "IE"=>"Irlanda",
          "IS"=>"Islandia",
          "IT"=>"Italia",
          "XK"=>"Kosovo",
          "LV"=>"Letonia",
          "LI"=>"Liechtenstein",
          "LT"=>"Lituania",
          "LU"=>"Luxemburgo",
          "MK"=>"Macedonia, República de",
          "MT"=>"Malta",
          "MD"=>"Moldavia",
          "MC"=>"Mónaco",
          "ME"=>"Montenegro",
          "NO"=>"Noruega",
          "NL"=>"Países Bajos",
          "PL"=>"Polonia", 
          "PT"=>"Portugal",
          "UK"=>"Reino Unido",
          "CZ"=>"República Checa",
          "RO"=>"Rumanía", 
          "RU"=>"Rusia",
          "SM"=>"San Marino",
          "SE"=>"Suecia", 
          "CH"=>"Suiza",
          "TR"=>"Turquía",
          "UA"=>"Ucrania",
          "YU"=>"Yugoslavia",
          "AO"=>"Angola",
          "DZ"=>"Argelia",
          "BJ"=>"Benín",
          "BW"=>"Botswana",
          "BF"=>"Burkina Faso",
          "BI"=>"Burundi",
          "CM"=>"Camerún",
          "CV"=>"Cabo Verde",
          "TD"=>"Chad",
          "KM"=>"Comores",
          "CG"=>"Congo",
          "CD"=>"Congo, República Democrática del",
          "CI"=>"Costa de Marfil",
          "EG"=>"Egipto",
          "ER"=>"Eritrea",
          "ET"=>"Etiopía",
          "GA"=>"Gabón",
          "GM"=>"Gambia",
          "GH"=>"Ghana",
          "GN"=>"Guinea",
          "GW"=>"Guinea Bissau",
          "GQ"=>"Guinea Ecuatorial",
          "KE"=>"Kenia",
          "LS"=>"Lesoto",
          "LR"=>"Liberia",
          "LY"=>"Libia",
          "MG"=>"Madagascar",
          "MW"=>"Malawi",
          "ML"=>"Malí",
          "MA"=>"Marruecos",
          "MU"=>"Mauricio",
          "MR"=>"Mauritania",
          "MZ"=>"Mozambique",
          "NA"=>"Namibia",
          "NE"=>"Níger",  
          "NG"=>"Nigeria",
          "CF"=>"República Centroafricana",
          "ZA"=>"República de Sudáfrica",
          "RW"=>"Ruanda",
          "EH"=>"Sahara Occidental",
          "ST"=>"Santo Tomé y Príncipe",
          "SN"=>"Senegal",  
          "SC"=>"Seychelles", 
          "SL"=>"Sierra Leona",
          "SO"=>"Somalia",
          "SD"=>"Sudán",
          "SS"=>"Sudán del Sur",
          "SZ"=>"Suazilandia",
          "TZ"=>"Tanzania",
          "TG"=>"Togo",
          "TN"=>"Túnez",
          "UG"=>"Uganda",
          "DJ"=>"Yibuti",
          "ZM"=>"Zambia",  
          "ZW"=>"Zimbabue",
          "AU"=>"Australia",
          "FM"=>"Micronesia, Estados Federados de",
          "FJ"=>"Fiji",
          "KI"=>"Kiribati",
          "MH"=>"Islas Marshall",
          "SB"=>"Islas Salomón",
          "NR"=>"Nauru",
          "NZ"=>"Nueva Zelanda",
          "PW"=>"Palaos",
          "PG"=>"Papúa Nueva Guinea",
          "WS"=>"Samoa",
          "TO"=>"Tonga",
          "TV"=>"Tuvalu", 
          "VU"=>"Vanuatu", 
          "AR"=>"Argentina",
          "BO"=>"Bolivia",
          "BR"=>"Brasil",
          "CL"=>"Chile",
          "CO"=>"Colombia",
          "EC"=>"Ecuador",
          "GY"=>"Guayana",
          "PY"=>"Paraguay",
          "PE"=>"Perú",
          "SR"=>"Surinam",
          "TT"=>"Trinidad y Tobago",
          "UY"=>"Uruguay",
          "VE"=>"Venezuela",
          "AG"=>"Antigua y Barbuda",
          "BS"=>"Bahamas",
          "BB"=>"Barbados",
          "BZ"=>"Belice",
          "CA"=>"Canadá",
          "CR"=>"Costa Rica",
          "CU"=>"Cuba",
          "DM"=>"Dominica",
          "SV"=>"El Salvador",
          "US"=>"Estados Unidos",
          "GD"=>"Granada",
          "GT"=>"Guatemala",
          "HT"=>"Haití",
          "HN"=>"Honduras",
          "JM"=>"Jamaica",
          "MX"=>"México",
          "NI"=>"Nicaragua",
          "PA"=>"Panamá",
          "PR"=>"Puerto Rico",
          "DO"=>"República Dominicana",
          "KN"=>"San Cristóbal y Nieves",
          "VC"=>"San Vicente y Granadinas",
          "LC"=>"Santa Lucía",
          "AF"=>"Afganistán",
          "SA"=>"Arabia Saudí",
          "BH"=>"Baréin",
          "BD"=>"Bangladesh",
          "MM"=>"Birmania",
          "BT"=>"Bután",
          "BN"=>"Brunéi",
          "KH"=>"Camboya",
          "CN"=>"China",
          "KP"=>"Corea, República Popular Democrática de",
          "KR"=>"Corea, República de",
          "AE"=>"Emiratos Árabes Unidos",
          "PH"=>"Filipinas",
          "IN"=>"India",
          "ID"=>"Indonesia",
          "IQ"=>"Iraq", 
          "IR"=>"Irán",
          "IL"=>"Israel",
          "JP"=>"Japón",
          "JO"=>"Jordania",
          "KZ"=>"Kazajistán",
          "KG"=>"Kirguizistán",
          "KW"=>"Kuwait",
          "LA"=>"Laos",
          "LB"=>"Líbano",
          "MY"=>"Malasia",
          "MV"=>"Maldivas",
          "MN"=>"Mongolia",
          "NP"=>"Nepal",
          "OM"=>"Omán",
          "PK"=>"Paquistán",
          "QA"=>"Qatar",
          "SG"=>"Singapur",
          "SY"=>"Siria",
          "LK"=>"Sri Lanka",
          "TJ"=>"Tayikistán",
          "TH"=>"Tailandia",
          "TP"=>"Timor Oriental",
          "TM"=>"Turkmenistán",
          "UZ"=>"Uzbekistán",
          "VN"=>"Vietnam",
          "YE"=>"Yemen",
        );
        
        return $countries[$code];
    }

    /**
     * Add messages
     *
     * @param \Desport\PanelBundle\Entity\Message $messages
     * @return Client
     */
    public function addMessage(\Desport\PanelBundle\Entity\Message $messages)
    {
        $this->messages[] = $messages;

        return $this;
    }

    /**
     * Remove messages
     *
     * @param \Desport\PanelBundle\Entity\Message $messages
     */
    public function removeMessage(\Desport\PanelBundle\Entity\Message $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
