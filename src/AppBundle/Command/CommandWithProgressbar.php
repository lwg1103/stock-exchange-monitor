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
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1024M');
        
        $this->prepare();
        $progress = new ProgressBar($output, count($this->items));
        $progress->start();

        foreach ($this->items as $item) {
            $this->doOneStep($item);
            $progress->advance();
        }

        $this->finish();
        $progress->finish();
    }
    
    abstract function doOneStep($item);
    abstract function prepare();
    abstract function finish();
}
