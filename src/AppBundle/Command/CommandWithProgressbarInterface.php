<?php
namespace AppBundle\Command;

interface CommandWithProgressbarInterface
{
    public function doOneStep($item);
    public function prepare();
}
