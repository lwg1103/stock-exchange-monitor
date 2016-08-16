<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Report;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\ReportType;

/**
 * Class ReportController
 *
 * @Route("/report")
 * 
 * @package AppBundle\Controller
 */
class ReportController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/add", name="report_add")
     * @Template
     *
     * @return Response
     */
    public function addAction(Request $request)
    {
        $report = new Report();

        $form = $this->createForm(ReportType::class, $report);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('app.use_case.add_report_manually')->add($form);

            return $this->redirectToRoute('company_get', ['marketId' => $report->getCompany()->getMarketId()]);
        }

        return [
            'form' => $form->createView()
        ];
    }
}
