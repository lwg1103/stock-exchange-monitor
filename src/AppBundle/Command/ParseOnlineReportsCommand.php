<?php
namespace AppBundle\Command;

use Company\Entity\Company;

class ParseOnlineReportsCommand extends ContainerAwareCommandWithProgressbar implements CommandWithProgressbarInterface
{

    public function prepare()
    {
        $this->items = $this->getContainer()
            ->get('app.use_case.list_companies')
            ->execute();
    }

    public function configure()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit','1024M');
        
        $this->setName('app:parse-online-reports')
            ->setDescription('Gets new reports from online for each company')
            ->setHelp("This command allows you to get new report from online sources");
    }

    public function doOneStep($item)
    {
        $this->getContainer()
            ->get('app.use_case.get_company_online_reports')
            ->parseLoadReport($item);
    }
}
