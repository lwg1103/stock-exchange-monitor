<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\NoResultException;

/**
 * Class CompanyController
 *
 * @Route("/company")
 *
 * @package AppBundle\Controller
 */
class CompanyController extends Controller
{
    /**
     * @Route("/", name="company_index")
     * @Template
     *
     * @return Response
     */
    public function indexAction()
    {
        $companies = [];

        $companiesWithoutPrices = $this->get('app.use_case.list_companies')->execute();

        foreach ($companiesWithoutPrices as $company) {
            $companies[$company->getMarketId()]['data'] = $company;
            try {
                $companies[$company->getMarketId()]['price'] = $this->get('app.use_case.get_price')->lastByCompany($company);
            } catch( NoResultException $e ) {
                $companies[$company->getMarketId()]['price'] = -1;
            }
            try {
                $companies[$company->getMarketId()]['CZ_last_year'] = $this->get('app.use_case.get_c_z_value')->getForLastYear($company);
            } catch ( NoResultException $e ) {
                $companies[$company->getMarketId()]['CZ_last_year'] = -1;
            }

            try {
                $companies[$company->getMarketId()]['CZ_last_4q'] = $this->get('app.use_case.get_c_z_value')->getForLastFourQuarters($company);
            } catch ( NoResultException $e ) {
                $companies[$company->getMarketId()]['CZ_last_4q'] = -1;
            }

        }

        return [
                'companies' => $companies
            ];
    }

    /**
     * @param string $marketId
     *
     * @Route("/{marketId}", name="company_get")
     * @Template
     *
     * @return Response
     */
    public function getAction($marketId)
    {
        $company            = $this->get('app.use_case.get_company')->byMarketId($marketId);
        try {
            $price              = $this->get('app.use_case.get_price')->lastByCompany($company);
        } catch ( NoResultException $e ) {
            $price = -1;
        }

        $historicalprices   = $this->get('app.use_case.get_price')->allByCompany($company);
        $reports            = $this->get('app.use_case.get_report')->allByCompany($company);
        $dividends          = $this->get('app.use_case.get_dividend')->allByCompany($company);

        return [
            'company'   => $company,
            'price'     => $price,
            'prices'    => $historicalprices,
            'reports'   => $reports,
            'dividends' => $dividends
        ];
    }
}
