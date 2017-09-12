<?php

namespace Application\UseCase;

use Company\Entity\Company;
use Doctrine\ORM\EntityRepository;
use Report\Entity\Report;

class GetReport
{
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * @param EntityRepository $entityRepository
     */
    public function __construct(EntityRepository $entityRepository)
    {
        $this->entityRepository = $entityRepository;
    }

    /**
     * @param Company       $company
     * @param \DateTime     $identifier
     * @param int           $period
     *
     * @return Report
     */
    public function oneByIdentifier($company, \DateTime $identifier, $period)
    {
        $manualReport = $this->entityRepository->findOneBy([
            'company' => $company,
            'identifier' => $identifier,
            'period' => $period,
            'type' => Report\Type::MANUAL
        ]);


        if (null != $manualReport) {
            return $manualReport;
        }

        return $this->entityRepository->findOneBy([
            'company' => $company,
            'identifier' => $identifier,
            'period' => $period,
            'type' => Report\Type::AUTO
        ]);
    }

    /**
     * @param Company $company
     *
     * @return Report[]
     */
    public function allByCompany(Company $company)
    {
        return $this->entityRepository->findBy(['company' => $company]);
    }

    /**
     * @param Company $company
     * 
     * @return Report
     */
    public function lastByCompany(Company $company)
    {
        $manualReport = $this->entityRepository->findOneBy(
            [
                'company' => $company,
                'type' => Report\Type::MANUAL
            ],
            [
                'identifier' => "DESC"
            ]
        );
        
        if (null != $manualReport)
            return $manualReport;

        return $this->entityRepository->findOneBy(
            [
                'company' => $company,
                'type' => Report\Type::AUTO
            ],
            [
                'identifier' => "DESC"
            ]
        );
    }

    /**
     * @param Company $company
     *
     * @return Report
     */
    public function lastYearByCompany(Company $company)
    {
        $manualReport = $this->entityRepository->findOneBy(
            [
                'company' => $company,
                'type' => Report\Type::MANUAL,
                'period' => Report\Period::ANNUAL
            ],
            [
                'identifier' => "DESC"
            ]
        );

        if (null != $manualReport)
            return $manualReport;

        return $this->entityRepository->findOneBy(
            [
                'company' => $company,
                'type' => Report\Type::AUTO,
                'period' => Report\Period::ANNUAL
            ],
            [
                'identifier' => "DESC"
            ]
        );
    }

    /**
     * @param Company $company
     *
     * @return Report
     */
    public function lastQuartersByCompany(Company $company)
    {
        $manualReport = $this->entityRepository->findBy(
            [
                'company' => $company,
                'type' => Report\Type::MANUAL,
                'period' => Report\Period::QUARTERLY
            ],
            [
                'identifier' => "DESC"
            ]
        );

        if (null != $manualReport)
            return $manualReport;

        return $this->entityRepository->findBy(
            [
                'company' => $company,
                'type' => Report\Type::AUTO,
                'period' => Report\Period::QUARTERLY
            ],
            [
                'identifier' => "DESC"
            ]
        );
    }
}