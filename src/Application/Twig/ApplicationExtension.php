<?php
/**
 * Created by PhpStorm.
 * User: macie
 * Date: 15.10.2017
 * Time: 22:08
 */

namespace Application\Twig;


class ApplicationExtension extends \Twig_Extension
{
    const INFLECTION_POINT = 1.5;
    const CLASS_CHEAP = 'green';
    const CLASS_EXPENSIVE = 'red';

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('colored_price', array($this, 'coloredPriceFilter')),
            new \Twig_SimpleFilter('colored_indicator', array($this, 'coloredIndicatorFilter')),
            new \Twig_SimpleFilter('price', array($this, 'priceFilter')),
            new \Twig_SimpleFilter('report_price', array($this, 'reportPriceFilter')),
            new \Twig_SimpleFilter('no_loss', array($this, 'noLossFilter')),
        );
    }

    public function coloredPriceFilter($price, $switchPoint = self::INFLECTION_POINT, $currency = "zł", $decimals = 2)
    {
        $class = $this->getClassForNumber($price, $switchPoint);

        return $this->formatPrice($price, $currency, $decimals, '<span class="price '.$class.'">', '</span>');
    }

    public function coloredIndicatorFilter($number, $switchPoint = self::INFLECTION_POINT)
    {
        $class = $this->getClassForNumber($number, $switchPoint);

        return '<span class="indicator '.$class.'">'.$number.'</span>';
    }

    public function noLossFilter($number)
    {
        switch ($number) {
            case 0:
                $txt = "FAILED";
                $class = self::CLASS_EXPENSIVE;
                break;
            case 1:
                $txt = "OK";
                $class = self::CLASS_CHEAP;
                break;
            default :
                $txt = "no data";
                $class = self::CLASS_EXPENSIVE;
                break;
        }

        return '<span class="indicator '.$class.'">'.$txt.'</span>';
    }

    public function priceFilter($price, $currency = "zł", $decimals = 2)
    {
        return $this->formatPrice($price, $currency, $decimals, '<span class="price">', '</span>');
    }

    public function reportPriceFilter($price, $currency = "zł")
    {
        return $this->formatPrice($price, " tys. ".$currency, 0);
    }

    public function getClassForNumber($number, $switchPoint)
    {
        $class = self::CLASS_CHEAP;
        if(((float)$number) > $switchPoint) {
            $class = self::CLASS_EXPENSIVE;
        }

        return $class;
    }

    private function formatPrice($price, $currency = "zł", $decimals = 2, $prefix = '', $suffix = '')
    {
        $price = number_format($price, $decimals, '.', ' ');
        $price = $price." ".$currency;

        return $prefix.$price.$suffix;
    }
}