<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Utils\ReportParser\Bankier\BiznesradarReportParser;
use Company\Entity\Company;
use Symfony\Component\Console\Helper\ProgressBar;


class ParseOnlineReportsCommand extends ContainerAwareCommand {
	
	protected function configure() {
		$this->setName('app:parse-online-reports')
				->setDescription('Gets new reports from online for each company')
				->setHelp("This command allows you to get new report from online sources");
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		
		$companies = $this->getContainer()->get('app.use_case.list_companies')->execute();
		
		$progress = new ProgressBar($output, count($companies));
		$progress->start();
		
		foreach($companies as $company) {
			$this->getContainer()->get('app.use_case.parse_report')->parseReportForCompany($company);
			$progress->advance();
		}
		
		$progress->finish();
	}
}
