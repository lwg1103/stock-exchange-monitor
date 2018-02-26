<?php

namespace Dividend\Entity;

use Company\Entity\Company;
use Dividend\Entity\Dividend\State;
use Doctrine\ORM\Mapping as ORM;

/**
 * Dividend
 *
 * @ORM\Entity
 * @ORM\Table(name="dividends", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="unique_dividend", columns={"period_from", "period_to", "company_id"})
 * })
 */
class Dividend
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
     * @ORM\Column(name="period_from", type="date", length=255)
     */
    private $periodFrom;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="period_to", type="date", length=255)
     */
    private $periodTo;

    /**
     * @var Company
     *
     * @ORM\ManyToOne(targetEntity="Company\Entity\Company", inversedBy="dividends")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="market_id")
     */
    private $company;
    
    /**
     * @var int
     *
     * @ORM\Column(name="value", type="integer")
     */
    private $value;
    
    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string")
     */
    private $currency;
    
    /**
     * @var float
     *
     * @ORM\Column(name="rate", type="decimal", precision=10, scale=5)
     *
     * Dividend to price rate in percentages
     */
    private $rate;
    
    /**
     * @var int
     *
     * @ORM\Column(name="state", type="smallint")
     */
    private $state;
    
    /**
     * @var date
     *
     * @ORM\Column(name="payment_date", type="date", length=255, nullable=true)
     */
    private $paymentDate;
    
    /**
     * @var date
     *
     * @ORM\Column(name="agm_date", type="date", length=255, nullable=true)
     *
     * General meeting date when dividend was agreed by shareholders
     */
    private $agmDate;
    

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
     * Set periodFrom
     *
     * @param \DateTime $periodFrom
     *
     * @return Dividend
     */
    public function setPeriodFrom($periodFrom)
    {
        $this->periodFrom = $periodFrom;

        return $this;
    }

    /**
     * Get periodFrom
     *
     * @return \DateTime
     */
    public function getPeriodFrom()
    {
        return $this->periodFrom;
    }

    /**
     * Set periodTo
     *
     * @param \DateTime $periodTo
     *
     * @return Dividend
     */
    public function setPeriodTo($periodTo)
    {
        $this->periodTo = $periodTo;

        return $this;
    }

    /**
     * Get periodTo
     *
     * @return \DateTime
     */
    public function getPeriodTo()
    {
        return $this->periodTo;
    }

    /**
     * Set value
     *
     * @param integer $value
     *
     * @return Dividend
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return Dividend
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return Dividend
     */
    public function setState($state)
    {
        if (!State::isValid($state)) {
            throw new \InvalidArgumentException("Valid states are: " . State::getValidKeys());
        }
        
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set paymentDate
     *
     * @param \DateTime $paymentDate
     *
     * @return Dividend
     */
    public function setPaymentDate($paymentDate)
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    /**
     * Get paymentDate
     *
     * @return \DateTime
     */
    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    /**
     * Set agmDate
     *
     * @param \DateTime $agmDate
     *
     * @return Dividend
     */
    public function setAgmDate($agmDate)
    {
        $this->agmDate = $agmDate;

        return $this;
    }

    /**
     * Get agmDate
     *
     * @return \DateTime
     */
    public function getAgmDate()
    {
        return $this->agmDate;
    }

    /**
     * Set company
     *
     * @param \Company\Entity\Company $company
     *
     * @return Dividend
     */
    public function setCompany(\Company\Entity\Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Company\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }
    
    /**
     * @return Money
     */
    public function getPrice()
    {
        return new Money($this->value, new Currency($this->currency));
    }

    /**
     * Set rate
     *
     * @param float $rate
     *
     * @return Dividend
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate
     *
     * @return float
     */
    public function getRate()
    {
        return $this->rate;
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        $dateFrom = $this->getPeriodFrom()->format('Y-m-d');
        $dateTo = $this->getPeriodTo()->format('Y-m-d');
        $state = State::toString($this->getState());
        $company = $this->getCompany()->__toString();
        $value = $this->getValue();
        $rate = $this->getRate();

        if($this->getState() == State::PAID && $this->getPaymentDate() != null) {
            $paidDate = $this->getPaymentDate()->format('Y-m-d');
            return sprintf("%s (%s - %s) %s (%s): %.2f (%.2f%%)", $company, $dateFrom, $dateTo, $state, $paidDate, $value/100, $rate);
        }
        return sprintf("%s (%s - %s) %s: %.2f (%.2f%%)", $company, $dateFrom, $dateTo, $state, $value/100, $rate);
    }
}
