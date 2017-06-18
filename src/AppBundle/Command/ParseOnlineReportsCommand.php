<?php
namespace AppBundle\Command;

use Company\Entity\Company;

class ParseOnlineReportsCommand extends ContainerAwareCommandWithProgressbar implements CommandWithProgressbarInterface
{

    protected function prepare()
    {
        $this->items = $this->getContainer()
            ->get('app.use_case.list_companies')
            ->execute();
    }

    protected function configure()
    {
        $this->setName('app:parse-online-reports')
            ->setDescription('Gets new reports from online for each company')
            ->setHelp("This command allows you to get new report from online sources");
    }

    protected function doOneStep($item)
    {
        $this->getContainer()
            ->get('app.use_case.get_company_online_reports')
            ->parseLoadReport($item);
    }
}
