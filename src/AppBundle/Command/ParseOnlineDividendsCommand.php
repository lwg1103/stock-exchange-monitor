<?php
namespace AppBundle\Command;

class ParseOnlineDividendsCommand extends ContainerAwareCommandWithProgressbar
{
    const MIN_YEAR = 2000;

    public function prepare()
    {
        $this->items = array();

        $now = Carbon::now('Europe/London')->year();

        for($i=self::MIN_YEAR; $i<=$now; $i++) {
            $this->items[] = $i;
        }
    }

    public function configure()
    {
        $this->setName('app:parse-online-dividends')
            ->setDescription('Gets dividends from oline sources')
            ->setHelp("This command allows you to get dividends from online sources");
    }

    public function doOneStep($item)
    {
        try {
            $this->getContainer()
                ->get('app.use_case.get_online_dividends_for_year')
                ->parseLoadDividends($item);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
