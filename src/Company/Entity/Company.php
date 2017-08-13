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
     * Company constructor.
     *
     * @param string $name
     * @param string $marketId
     * @param string $type
     */
    public function __construct($name, $marketId, $type = Type::ORDINARY)
    {
        $this->name     = $name;
        $this->marketId = $marketId;
        $this->type 	= $type;
        $this->reports  = new ArrayCollection();
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
     * @return string
     */
    public function __toString()
    {
        return $this->getMarketId();
    }
}

