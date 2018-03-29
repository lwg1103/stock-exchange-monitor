<?php

namespace Company\Entity\Company;

use Company\Entity\Company\Group\Type;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Group
 *
 * @ORM\Table(name="company_groups")
 * @ORM\Entity
 */
class Group
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var Company[]
     *
     * @ORM\ManyToMany(targetEntity="Company\Entity\Company", mappedBy="groups")
     */
    private $companies;

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
     * @param string $longMarketId
     * @param string $type
     */
    public function __construct($name, $type = Type::INDEX,
        $longMarketId = null)
    {
        $this->name     = $name;
        $this->type     = $type;
        $this->companies  = new ArrayCollection();
    }

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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return string
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * @return Company[]
     */
    public function getCompanies()
    {
        return $this->companies;
    }

    /**
     * @param Company[] $company
     *
     * @return self
     */
    public function setCompanies($companies)
    {
        $this->companies = $companies;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    public function getTypeString()
    {
        return Type::toString($this->getType());
    }
}

