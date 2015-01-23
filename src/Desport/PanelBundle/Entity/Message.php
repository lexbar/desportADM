<?php

namespace Desport\PanelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Message
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
     * @ORM\Column(name="subject", type="string", length=255)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="emailFrom", type="string", length=255)
     */
    private $emailFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="emailTo", type="string", length=255)
     */
    private $emailTo;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="textHTML", type="text")
     */
    private $textHTML;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="messagesSent")
     * @ORM\JoinColumn(name="user_from_id", referencedColumnName="id")
     */
    private $userFrom;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="messagesReceived")
     * @ORM\JoinColumn(name="user_to_id", referencedColumnName="id")
     */
    private $userTo;

    /**
     * @ORM\OneToOne(targetEntity="Ticket", mappedBy="message")
     **/
    private $ticket;

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="messages")
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
     * Set subject
     *
     * @param string $subject
     * @return Message
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set emailFrom
     *
     * @param string $emailFrom
     * @return Message
     */
    public function setEmailFrom($emailFrom)
    {
        $this->emailFrom = $emailFrom;

        return $this;
    }

    /**
     * Get emailFrom
     *
     * @return string 
     */
    public function getEmailFrom()
    {
        return $this->emailFrom;
    }

    /**
     * Set emailTo
     *
     * @param string $emailTo
     * @return Message
     */
    public function setEmailTo($emailTo)
    {
        $this->emailTo = $emailTo;

        return $this;
    }

    /**
     * Get emailTo
     *
     * @return string 
     */
    public function getEmailTo()
    {
        return $this->emailTo;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Message
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
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

    /**
     * Set textHTML
     *
     * @param string $textHTML
     * @return Message
     */
    public function setTextHTML($textHTML)
    {
        $this->textHTML = $textHTML;

        return $this;
    }

    /**
     * Get textHTML
     *
     * @return string 
     */
    public function getTextHTML()
    {
        return $this->textHTML;
    }

    /**
     * Set userFrom
     *
     * @param boolean $userFrom
     * @return Message
     */
    public function setUserFrom($userFrom)
    {
        $this->userFrom = $userFrom;

        return $this;
    }

    /**
     * Get userFrom
     *
     * @return boolean 
     */
    public function getUserFrom()
    {
        return $this->userFrom;
    }

    /**
     * Set userTo
     *
     * @param boolean $userTo
     * @return Message
     */
    public function setUserTo($userTo)
    {
        $this->userTo = $userTo;

        return $this;
    }

    /**
     * Get userTo
     *
     * @return boolean 
     */
    public function getUserTo()
    {
        return $this->userTo;
    }

    /**
     * Set ticket
     *
     * @param boolean $ticket
     * @return Message
     */
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Get ticket
     *
     * @return boolean 
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * Set client
     *
     * @param boolean $client
     * @return Message
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
}
