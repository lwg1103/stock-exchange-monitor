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
        return [
                'companies' => $this->get('app.use_case.list_companies')->execute()
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
        $reports            = $this->get('app.use_case.list_reports')->byCompany($company);

        return [
            'company' => $company,
            'price'   => $price,
            'prices'  => $historicalprices,
            'reports' => $reports
        ];
    }
}
