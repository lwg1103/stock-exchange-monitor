<?php

namespace Company\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Report\Entity\Report;
use Company\Entity\Company\Type;
use Doctrine\ORM\Mapping as ORM;

/**
 * Company
 *
 * @ORM\Table(name="companies")
 * @ORM\Entity
 */
class Company
{
    /**
     * @var string
     *
     * @ORM\Column(name="market_id", type="string", length=255, unique=true)
     * @ORM\Id
     */
    private $marketId;

    /**
     * @var string
     *
     * @ORM\Column(name="long_market_id", type="string", length=255, unique=true, nullable=true, options={"default":null})
     */
    private $longMarketId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var Report[]
     *
     * @ORM\OneToMany(targetEntity="Report\Entity\Report", mappedBy="company")
     */
    private $reports;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;
    
    /**
     * many Companies have many Groups.
     * @ORM\ManyToMany(targetEntity="Company\Entity\Company\Group", inversedBy="companies")
     * @ORM\JoinTable(name="companies_groups_rel",
     *      joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="market_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id", unique=true)})
     */
    private $groups;

    /**
     * Company constructor.
     *
     * @param string $name
     * @param string $marketId
     * @param string $longMarketId
     * @param string $type
     */
    public function __construct($name, $marketId, $type = Type::ORDINARY,
        $longMarketId = null)
    {
        $this->name     = $name;
        $this->marketId = $marketId;
        $this->type 	= $type;
        $this->longMarketId = $longMarketId;
        $this->reports  = new ArrayCollection();
        $this->groups   = new ArrayCollection();
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
     * Get marketId
     *
     * @return string
     */
    public function getMarketId()
    {
        return $this->marketId;
    }

    /**
     * Get longMarketId
     *
     * @return string
     */
    public function getLongMarketId()
    {
        return $this->longMarketId;
    }

    /**
     * Set longMarketId
     *
     * @return Comapny
     */
    public function setLongMarketId($longMarketId)
    {
        $this->longMarketId = $longMarketId;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
    	return $this->type;
    }

    /**
     * @return Report
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * @param Report[] $reports
     *
     * @return self
     */
    public function setReports($reports)
    {
        $this->reports = $reports;

        return $this;
    }

    /**
     * @return Group[]
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param Group[] $groups
     *
     * @return self
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
    
        return $this;
    }
    

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getMarketId();
    }
}

