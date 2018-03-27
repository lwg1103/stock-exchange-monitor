<?php

namespace Company\Entity\Company;

use Company\Entity\Group\Type;
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
}

