<?php
namespace AppBundle\Command;

class ParseOnlineMarketIdsCommand extends ContainerAwareCommandWithProgressbar
{
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

        $this->setName('app:parse-marketids')
            ->setDescription('Gets long marketId and biznseradar marketId')
            ->setHelp("");
    }

    public function doOneStep($item)
    {
        try {
            $this->setMarketIdsFromBiznesradar($item);
            $this->em->persist($item);
            $this->em->flush();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
    
    private function setMarketIdsFromBiznesradar($item) {
        $html = file_get_contents("https://www.biznesradar.pl/raporty-finansowe-rachunek-zyskow-i-strat/".$item->getMarketId());
        $re = '/<h1>Raporty finansowe: Rachunek zysków i strat.*\((.*)\)<\/h1>/';
        preg_match_all($re, $html, $longMarketIdMatches, PREG_SET_ORDER, 0);
        
        $longMarketId = $item->getMarketId();
        
        if(count($longMarketIdMatches) && count($longMarketIdMatches[0]) >= 1) {
            
            $longMarketId = $longMarketIdMatches[0][1];
        }
        
        $item->setLongMarketId($longMarketId);
    }
    
    public function finish() {
        $this->em->flush();
    }
}
