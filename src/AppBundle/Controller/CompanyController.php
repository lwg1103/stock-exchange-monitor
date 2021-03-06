<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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
            $companies[$company->getMarketId()]['price'] = $this->get('app.use_case.get_price')->lastByCompany($company);
            $companies[$company->getMarketId()]['CZ_last_year'] = $this->get('app.use_case.get_c_z_value')->getForLastYear($company);
            $companies[$company->getMarketId()]['CZ_last_4q'] = $this->get('app.use_case.get_c_z_value')->getForLastFourQuarters($company);
            $companies[$company->getMarketId()]['CZ_last_7y'] = $this->get('app.use_case.get_c_z_value')->getForLastSevenYears($company);
            $companies[$company->getMarketId()]['CWK_last_year'] = $this->get('app.use_case.get_c_wk_value')->getForLastYear($company);
            $companies[$company->getMarketId()]['CZCWK_last_year'] = $this->get('app.use_case.get_c_z_c_wk_value')->getForLastYear($company);
            $companies[$company->getMarketId()]['div_rate_last_7y'] = $this->get('app.use_case.get_dividend_rate_value')->getForLastSevenYears($company);
            $companies[$company->getMarketId()]['no_losses'] = $this->get('app.use_case.get_no_loss_value')->getForLastSevenYears($company);
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
        $price              = $this->get('app.use_case.get_price')->lastByCompany($company);
        $historicalprices   = $this->get('app.use_case.get_price')->allByCompany($company);
        $annualReports      = $this->get('app.use_case.get_report')->lastYearsByCompany($company);
        $quarterlyReports      = $this->get('app.use_case.get_report')->lastQuartersByCompany($company, 5);
        $dividends          = $this->get('app.use_case.get_dividend')->allByCompany($company, 'asc');

        return [
            'company'   => $company,
            'price'     => $price,
            'prices'    => $historicalprices,
            'annualReports'   => $annualReports,
            'quarterlyReports'   => $quarterlyReports,
            'dividends' => $dividends
        ];
    }
}
