<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PullPriceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:pull-price')
            ->setDescription('Pulls prices for one or all companies.')
            ->addArgument('market-id', InputArgument::OPTIONAL, 'Pull price only for this company');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $marketId = $input->getArgument('market-id');

        if ($marketId) {
            $output->writeln('pulling price for ' . $marketId);
            $this->pullPriceForCompany($marketId);
        } else {
            $output->writeln('pulling price for all companies');
            $this->pullPriceForAllCompanies();
        }
    }

    private function pullPriceForCompany($marketId)
    {
        $this->getContainer()->get('app.use_case.pull_price')->pullPrice(strtoupper($marketId));
    }

    private function pullPriceForAllCompanies()
    {
        $this->getContainer()->get('app.use_case.pull_all_prices')->pullAllPrices();
    }
}