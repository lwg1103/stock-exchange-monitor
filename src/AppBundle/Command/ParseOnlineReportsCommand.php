<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Utils\ReportParser\Bankier\BiznesradarReportParser;
use AppBundle\Entity\Company;

class ParseOnlineReportsCommand extends ContainerAwareCommand {
	
	protected function configure() {
		$this->setName('app:parse-online-reports')
				->setDescription('Gets new reports from online for each company')
				->setHelp("This command allows you to get new report from online sources");
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		
		$companies = $this->getContainer()->get('app.use_case.list_companies')->execute();
		//$bankierReportParser = $this->getContainer()->get('app.utils.report_parser_bankier');
		$biznesradarReportParser = $this->getContainer()->get('app.utils.report_parser_biznesradar');
		foreach($companies as $company) {
			$report = $biznesradarReportParser->parse($company);
			
		}
	}
}
