<?php

namespace AppBundle\Command;

use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PullPriceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:pull-price')
            ->setDescription('Pulls prices for one or all companies.')
            ->addOption('day', 'd', InputOption::VALUE_REQUIRED, 'Pull price for this day', 'yesterday')
            ->addOption('market-id', 'm',  InputOption::VALUE_REQUIRED, 'Pull price only for this company')
            ->addOption('all-dates', 'a', InputOption::VALUE_NONE, 'Pull price for all available dates since 1st January 2007');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $marketId = $input->getOption('market-id');
        $date = Carbon::parse($input->getOption('day'));
        $isForAll = $input->getOption('all-dates');

        if ($isForAll) {
            if (!$marketId) {
                throw new \InvalidArgumentException("MarketId is required with option 'all-dates'");
            }

            for ($date = Carbon::createFromDate(2010,1,1); $date < Carbon::now(); $date->nextWeekday()) {
                try {
                    $this->pullForDate($output, $marketId, $date);
                } catch (\Exception $e) { /** sometimes given company is missing from file */ }
            }
        } else {
            $this->pullForDate($output, $marketId, $date);
        }
    }

    private function pullPriceForCompany($marketId, $date)
    {
        $this->getContainer()->get('app.use_case.pull_price')->pullPrice(strtoupper($marketId), $date);
    }

    private function pullPriceForAllCompanies($date)
    {
        $this->getContainer()->get('app.use_case.pull_all_prices')->pullAllPrices($date);
    }

    protected function pullForDate(OutputInterface $output, $marketId, \DateTime $date)
    {
        if ($marketId) {
            $output->writeln('pulling price for ' . $marketId . ' for ' . $date->format("Y-m-d"));
            $this->pullPriceForCompany($marketId, $date);
        } else {
            $output->writeln('pulling price for all companies for ' . $date->format("Y-m-d"));
            $this->pullPriceForAllCompanies($date);
        }
    }
}