<?php

namespace Report\Entity;

use Report\Entity\Report\Period;
use Report\Entity\Report\Type;
use Company\Entity\Company;
use Doctrine\ORM\Mapping as ORM;

/**
 * Report
 *
 * @ORM\Entity
 * @ORM\Table(name="reports", uniqueConstraints={
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
     * @ORM\ManyToOne(targetEntity="Company\Entity\Company", inversedBy="reports")
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
     * @var float
     *
     * @ORM\Column(name="income", type="float")
     */
    private $income;

    /**
     * @var float
     *
     * @ORM\Column(name="netProfit", type="float")
     */
    private $netProfit;

    /**
     * @var float
     *
     * @ORM\Column(name="operationalNetProfit", type="float", nullable=true)
     */
    private $operationalNetProfit;

    /**
     * @var float
     *
     * @ORM\Column(name="bookValue", type="float")
     */
    private $bookValue;

    /**
     * @var float
     *
     * @ORM\Column(name="assets", type="float")
     */
    private $assets;

    /**
     * @var float
     *
     * @ORM\Column(name="currentAssets", type="float", nullable=true)
     */
    private $currentAssets;

    /**
     * @var float
     *
     * @ORM\Column(name="liabilities", type="float", nullable=true)
     */
    private $liabilities;

    /**
     * @var float
     *
     * @ORM\Column(name="currentLiabilities", type="float", nullable=true)
     */
    private $currentLiabilities;

    /**
     * @var float
     *
     * @ORM\Column(name="sharesQuantity", type="float")
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
     * @param float $income
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
     * @return float
     */
    public function getIncome()
    {
        return $this->income;
    }

    /**
     * Set netProfit
     *
     * @param float $netProfit
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
     * @return float
     */
    public function getNetProfit()
    {
        return $this->netProfit;
    }

    /**
     * Set operationalNetProfit
     *
     * @param float $operationalNetProfit
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
     * @return float
     */
    public function getOperationalNetProfit()
    {
        return $this->operationalNetProfit;
    }

    /**
     * Set bookValue
     *
     * @param float $bookValue
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
     * @return float
     */
    public function getBookValue()
    {
        return $this->bookValue;
    }

    /**
     * Set assets
     *
     * @param float $assets
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
     * @return float
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * Set currentAssets
     *
     * @param float $currentAssets
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
     * @return float
     */
    public function getCurrentAssets()
    {
        return $this->currentAssets;
    }

    /**
     * Set liabilities
     *
     * @param float $liabilities
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
     * @return float
     */
    public function getLiabilities()
    {
        return $this->liabilities;
    }

    /**
     * Set currentLiabilities
     *
     * @param float $currentLiabilities
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
     * @return float
     */
    public function getCurrentLiabilities()
    {
        return $this->currentLiabilities;
    }

    /**
     * Set sharesQuantity
     *
     * @param float $sharesQuantity
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
     * @return float
     */
    public function getSharesQuantity()
    {
        return (int)$this->sharesQuantity;
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

