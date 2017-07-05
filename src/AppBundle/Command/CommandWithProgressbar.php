<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

abstract class CommandWithProgressbar extends Command implements CommandWithProgressbarInterface
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
    
    abstract function doOneStep($item);
    abstract function prepare();
}
