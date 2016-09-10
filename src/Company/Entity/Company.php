<?php

namespace Company\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Report\Entity\Report;
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
     * Company constructor.
     *
     * @param string $name
     * @param string $marketId
     */
    public function __construct($name, $marketId)
    {
        $this->name     = $name;
        $this->marketId = $marketId;
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

