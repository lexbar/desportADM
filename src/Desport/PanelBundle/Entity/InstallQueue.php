<?php

namespace Desport\PanelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InstallQueue
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class InstallQueue
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
     * @ORM\ManyToOne(targetEntity="Site", inversedBy="installQueues")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     */
    private $site;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="stage", type="integer")
     */
    private $stage;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reverse", type="boolean")
     */
    private $reverse=false;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="done", type="boolean")
     */
    private $done=false;

    /**
     * @var integer
     *
     * @ORM\Column(name="trials", type="integer")
     */
    private $trials=0;
    
    private $lastStage = 3; //The last possible id for stages


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
     * Set date
     *
     * @param \DateTime $date
     * @return InstallQueue
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
     * Set stage
     *
     * @param integer $stage
     * @return InstallQueue
     */
    public function setStage($stage)
    {
        $this->stage = $stage;

        return $this;
    }
    
    /**
     * Set next stage
     *
     * @param \Desport\PanelBundle\Entity\InstallQueue $installQueue
     * @return InstallQueue
     */
    public function setNextStage($installQueue)
    {
        if($installQueue->getReverse())
        {
            if($installQueue->getStage() == 0)
            {
                $this->stage = 1;
                $this->setReverse(false);
            }
            else
            {
                $this->stage = $installQueue->getStage() - 1;
            }
            
        }
        else
        {
            $this->stage = $installQueue->getStage() + 1;
        }

        return $this;
    }

    /**
     * Get stage
     *
     * @return integer 
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * Set reverse
     *
     * @param boolean $reverse
     * @return InstallQueue
     */
    public function setReverse($reverse)
    {
        $this->reverse = $reverse;

        return $this;
    }

    /**
     * Get reverse
     *
     * @return boolean 
     */
    public function getReverse()
    {
        return $this->reverse;
    }

    /**
     * Set trials
     *
     * @param integer $trials
     * @return InstallQueue
     */
    public function setTrials($trials)
    {
        $this->trials = $trials;

        return $this;
    }
    
    /**
     * Increase trials
     *
     * @param integer $trials
     * @return InstallQueue
     */
    public function increaseTrials()
    {
        $this->trials = $this->trials + 1 ;

        return $this;
    }

    /**
     * Get trials
     *
     * @return integer 
     */
    public function getTrials()
    {
        return $this->trials;
    }
    
    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->setDate(new \DateTime('now'));
    }

    /**
     * Set site
     *
     * @param \Desport\PanelBundle\Entity\Site $site
     * @return InstallQueue
     */
    public function setSite(\Desport\PanelBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return \Desport\PanelBundle\Entity\Site 
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set done
     *
     * @param boolean $done
     * @return InstallQueue
     */
    public function setDone($done)
    {
        $this->done = $done;

        return $this;
    }

    /**
     * Get done
     *
     * @return boolean 
     */
    public function getDone()
    {
        return $this->done;
    }
}
