<?php
namespace AppBundle\Command;

use Company\Entity\Company;

class ParseOnlineDividendsCommand extends ContainerAwareCommandWithProgressbar
{
    const MIN_YEAR = 2016;//2001;

    public function prepare()
    {
        $this->items = array();

        $now = (int)date("Y");

        for($i=self::MIN_YEAR; $i<=$now; $i++) {
            $this->items[] = $i;
        }
    }

    public function configure()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1024M');

        $this->setName('app:parse-online-dividends')
            ->setDescription('Gets dividends from oline sources')
            ->setHelp("This command allows you to get dividends from online sources");
    }

    public function doOneStep($item)
    {
        try {
            $this->getContainer()
                ->get('app.use_case.get_online_dividends_for_year')
                //->parseLoadReport($item);
                ->parseDividendsForYear($item);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
