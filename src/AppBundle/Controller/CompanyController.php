<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
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
     * @Route("/{marketId}", name="company_get")
     * @Template
     *
     * @param string $marketId
     *
     * @return Response
     */
    public function getAction($marketId)
    {
        return [
            'company' => $this->get('app.use_case.get_company')->byMarketId($marketId)
        ];
    }
}
