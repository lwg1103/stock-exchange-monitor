<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CompanyController
 * 
 * @package AppBundle\Controller
 */
class HomepageController extends Controller
{
    /**
     * @Route("/", name="homepage_index")
     * @Template
     *
     * @return Response
     */
    public function indexAction()
    {
        return [];
    }
}
