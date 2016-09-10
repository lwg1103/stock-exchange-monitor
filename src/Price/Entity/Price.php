<?php

namespace Price\Entity;

use Company\Entity\Company;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;

/**
 * Company
 *
 * @ORM\Table(name="prices")
 * @ORM\Entity
 */
class Price 
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
     * @var Company
     *
     * @ORM\ManyToOne(targetEntity="Company\Entity\Company", inversedBy="reports")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="market_id")
     */
    private $company;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="identifier", type="date", length=255)
     */
    private $identifier;

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
     * Price constructor.
     * @param Company $company
     * @param Money $priceValue
     */
    public function __construct(Company $company, Money $priceValue)
    {
        $this->company = $company;
        $this->identifier = Carbon::today('Europe/Warsaw');
        $this->setPriceValue($priceValue);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return \DateTime
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return Money
     */
    public function getPrice()
    {
        return new Money($this->value, new Currency($this->currency));
    }

    /**
     * @param Money $priceValue
     * 
     * @return self
     */
    private function setPriceValue(Money $priceValue)
    {
        $this->value    = $priceValue->getAmount();
        $this->currency = $priceValue->getCurrency()->getName();
        
        return $this;
    }
}