<?php

namespace Price\Entity;

use Company\Entity\Company;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

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
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;

    /**
     * Price constructor.
     * @param Company           $company
     * @param float             $value
     * @param null|DateTime     $identifier
     */
    public function __construct(Company $company, $value, $identifier = null)
    {
        $this->company = $company;
        $this->identifier = ($identifier) ? $identifier : Carbon::today('Europe/Warsaw');
        $this->value = $value;
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
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param float $value
     * 
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;
        
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%.2f", $this->value);
    }
}