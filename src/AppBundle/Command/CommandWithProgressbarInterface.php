<?php
namespace AppBundle\Command;

interface CommandWithProgressbarInterface
{
    protected function doOneStep();
    protected function prepare();
    protected function configure();
}
