<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Report\Period;
use AppBundle\Entity\Report\Type;
use Doctrine\ORM\Mapping as ORM;

/**
 * Report
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReportRepository")
 * @ORM\Table(name="report", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="unique_report", columns={"identifier", "company_id", "type", "period"})
 * })
 */
class Report
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="identifier", type="date", length=255)
     */
    private $identifier;

    /**
     * @var Company
     *
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="reports")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="market_id")
     */
    private $company;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="period", type="smallint")
     */
    private $period;

    /**
     * @var int
     *
     * @ORM\Column(name="income", type="integer")
     */
    private $income;

    /**
     * @var int
     *
     * @ORM\Column(name="netProfit", type="integer")
     */
    private $netProfit;

    /**
     * @var int
     *
     * @ORM\Column(name="operationalNetProfit", type="integer", nullable=true)
     */
    private $operationalNetProfit;

    /**
     * @var int
     *
     * @ORM\Column(name="bookValue", type="integer")
     */
    private $bookValue;

    /**
     * @var int
     *
     * @ORM\Column(name="assets", type="integer")
     */
    private $assets;

    /**
     * @var int
     *
     * @ORM\Column(name="currentAssets", type="integer", nullable=true)
     */
    private $currentAssets;

    /**
     * @var int
     *
     * @ORM\Column(name="liabilities", type="integer", nullable=true)
     */
    private $liabilities;

    /**
     * @var int
     *
     * @ORM\Column(name="currentLiabilities", type="integer", nullable=true)
     */
    private $currentLiabilities;

    /**
     * @var int
     *
     * @ORM\Column(name="sharesQuantity", type="bigint")
     */
    private $sharesQuantity;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param \DateTime $identifier
     *
     * @return self
     */
    public function setIdentifier(\DateTime $identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     *
     * @return self
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Report
     */
    public function setType($type)
    {
        if (!Type::isValid($type)) {
            throw new \InvalidArgumentException("Valid values are: " . Type::getValidKeys());
        }

        $this->type = $type;

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
     * Set period
     *
     * @param integer $period
     *
     * @return Report
     */
    public function setPeriod($period)
    {
        if (!Period::isValid($period)) {
            throw new \InvalidArgumentException("Valid values are: " . Period::getValidKeys());
        }

        $this->period = $period;

        return $this;
    }

    /**
     * Get period
     *
     * @return int
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Set income
     *
     * @param integer $income
     *
     * @return Report
     */
    public function setIncome($income)
    {
        $this->income = $income;

        return $this;
    }

    /**
     * Get income
     *
     * @return int
     */
    public function getIncome()
    {
        return $this->income;
    }

    /**
     * Set netProfit
     *
     * @param integer $netProfit
     *
     * @return Report
     */
    public function setNetProfit($netProfit)
    {
        $this->netProfit = $netProfit;

        return $this;
    }

    /**
     * Get netProfit
     *
     * @return int
     */
    public function getNetProfit()
    {
        return $this->netProfit;
    }

    /**
     * Set operationalNetProfit
     *
     * @param integer $operationalNetProfit
     *
     * @return Report
     */
    public function setOperationalNetProfit($operationalNetProfit)
    {
        $this->operationalNetProfit = $operationalNetProfit;

        return $this;
    }

    /**
     * Get operationalNetProfit
     *
     * @return int
     */
    public function getOperationalNetProfit()
    {
        return $this->operationalNetProfit;
    }

    /**
     * Set bookValue
     *
     * @param integer $bookValue
     *
     * @return Report
     */
    public function setBookValue($bookValue)
    {
        $this->bookValue = $bookValue;

        return $this;
    }

    /**
     * Get bookValue
     *
     * @return int
     */
    public function getBookValue()
    {
        return $this->bookValue;
    }

    /**
     * Set assets
     *
     * @param integer $assets
     *
     * @return Report
     */
    public function setAssets($assets)
    {
        $this->assets = $assets;

        return $this;
    }

    /**
     * Get assets
     *
     * @return int
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * Set currentAssets
     *
     * @param integer $currentAssets
     *
     * @return Report
     */
    public function setCurrentAssets($currentAssets)
    {
        $this->currentAssets = $currentAssets;

        return $this;
    }

    /**
     * Get currentAssets
     *
     * @return int
     */
    public function getCurrentAssets()
    {
        return $this->currentAssets;
    }

    /**
     * Set liabilities
     *
     * @param integer $liabilities
     *
     * @return Report
     */
    public function setLiabilities($liabilities)
    {
        $this->liabilities = $liabilities;

        return $this;
    }

    /**
     * Get liabilities
     *
     * @return int
     */
    public function getLiabilities()
    {
        return $this->liabilities;
    }

    /**
     * Set currentLiabilities
     *
     * @param integer $currentLiabilities
     *
     * @return Report
     */
    public function setCurrentLiabilities($currentLiabilities)
    {
        $this->currentLiabilities = $currentLiabilities;

        return $this;
    }

    /**
     * Get currentLiabilities
     *
     * @return int
     */
    public function getCurrentLiabilities()
    {
        return $this->currentLiabilities;
    }

    /**
     * Set sharesQuantity
     *
     * @param integer $sharesQuantity
     *
     * @return Report
     */
    public function setSharesQuantity($sharesQuantity)
    {
        $this->sharesQuantity = $sharesQuantity;

        return $this;
    }

    /**
     * Get sharesQuantity
     *
     * @return int
     */
    public function getSharesQuantity()
    {
        return $this->sharesQuantity;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $date = $this->getIdentifier()->format('d-m-Y');
        $period = Period::toString($this->getPeriod());
        $type = Type::toString($this->getType());

        return sprintf("%s (%s, %s)", $date, $period, $type);
    }
}

