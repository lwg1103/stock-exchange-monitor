<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

abstract class ContainerAwareCommandWithProgressbar extends ContainerAwareCommand implements CommandWithProgressbarInterface
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
    
    abstract public function doOneStep($item);
    abstract public function prepare();
}
