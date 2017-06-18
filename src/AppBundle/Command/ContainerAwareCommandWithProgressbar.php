<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class ContainerAwareCommandWithProgressbar extends ContainerAwareCommand
{

    var $items;

    protected function execute(InputInterface $input, OutputInterface $output)
    {   $this->prepare();
        $progress = new ProgressBar($output, count($this->items));
        $progress->start();
        
        foreach ($this->items as $item) {
            $this->doOneStep($item);
            $progress->advance();
        }
        
        $progress->finish();
    }
}
