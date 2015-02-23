<?php

namespace Desport\PanelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AutoMessageCategory
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class AutoMessageCategory
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
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="AutoMessageCategory", inversedBy="childs")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;
    
    /**
     * @ORM\OneToMany(targetEntity="AutoMessageCategory", mappedBy="parent")
     */
    private $childs;
    
    /**
     * @ORM\OneToMany(targetEntity="AutoMessage", mappedBy="category")
     */
    private $automessages;


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
     * @return AutoresponseCategory
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
     * Set date
     *
     * @param \DateTime $date
     * @return AutoresponseCategory
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
     * Set parent
     *
     * @param boolean $parent
     * @return AutoresponseCategory
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return boolean 
     */
    public function getParent()
    {
        return $this->parent;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->childs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->automessages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add childs
     *
     * @param \Desport\PanelBundle\Entity\AutoMessageCategory $childs
     * @return AutoMessageCategory
     */
    public function addChild(\Desport\PanelBundle\Entity\AutoMessageCategory $childs)
    {
        $this->childs[] = $childs;

        return $this;
    }

    /**
     * Remove childs
     *
     * @param \Desport\PanelBundle\Entity\AutoMessageCategory $childs
     */
    public function removeChild(\Desport\PanelBundle\Entity\AutoMessageCategory $childs)
    {
        $this->childs->removeElement($childs);
    }

    /**
     * Get childs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * Add automessages
     *
     * @param \Desport\PanelBundle\Entity\AutoMessage $automessages
     * @return AutoMessageCategory
     */
    public function addAutomessage(\Desport\PanelBundle\Entity\AutoMessage $automessages)
    {
        $this->automessages[] = $automessages;

        return $this;
    }

    /**
     * Remove automessages
     *
     * @param \Desport\PanelBundle\Entity\AutoMessage $automessages
     */
    public function removeAutomessage(\Desport\PanelBundle\Entity\AutoMessage $automessages)
    {
        $this->automessages->removeElement($automessages);
    }

    /**
     * Get automessages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAutomessages()
    {
        return $this->automessages;
    }
}
