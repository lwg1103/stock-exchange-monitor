<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CompanyController
 *
 * @Route("/company_group")
 *
 * @package AppBundle\Controller
 */
class GroupController extends Controller
{
    /**
     * @Route("/", name="company_group_index")
     * @Template
     *
     * @return Response
     */
    public function indexAction()
    {
        $groups = $this->get('app.use_case.list_company_groups')->execute();

        return [
                'groups' => $groups
            ];
    }

    /**
     * @param string $marketId
     *
     * @Route("/{id}", name="company_group_get")
     * @Template
     *
     * @return Response
     */
    public function getAction($id)
    {
        $group = $this->get('app.use_case.get_company_group')->byId($id);

        return [
            'group'   => $group,
        ];
    }
}
