<?php
namespace AppBundle\Command;

class ParseOnlineLongMarketIdCommand extends ContainerAwareCommandWithProgressbar
{
    const MIN_YEAR = 2016;//2001;

    private $em = null;

    public function prepare()
    {
        $this->items = $this->getContainer()->get('app.use_case.list_companies')->execute();
        $this->em = $this->getContainer()->get('doctrine')->getEntityManager();
    }

    public function configure()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1024M');

        $this->setName('app:parse-online-longmarketid')
            ->setDescription('Gets long market id from biznseradar')
            ->setHelp("");
    }

    public function doOneStep($item)
    {
        try {
            $html = file_get_contents("https://www.biznesradar.pl/notowania/".$item->getMarketId());
            $re = '/<h1>Notowania .* \((.*)\)<\/h1>/';
            preg_match_all($re, $html, $matches, PREG_SET_ORDER, 0);

            $longMarketId = $item->getMarketId();
            if(count($matches) && count($matches[0]) >= 1) {
                $longMarketId = $matches[0][1];
            }
            $item->setLongMarketId($longMarketId);
            $this->em->persist($item);
            $this->em->flush();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function finish() {
        $this->em->flush();
    }
}
