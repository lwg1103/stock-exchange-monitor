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
        );
    }

    public function coloredPriceFilter($price, $switchPoint = self::INFLECTION_POINT, $currency = "zł", $decimals = 2, $decPoint = '.', $thousandsSep = ' ')
    {
        $class = $this->getClassForNumber($price, $switchPoint);
        
        $price = number_format($price, $decimals, $decPoint, $thousandsSep);
        $price = $price." ".$currency;

        return '<span class="price '.$class.'">'.$price.'</span>';
    }

    public function coloredIndicatorFilter($number, $switchPoint = self::INFLECTION_POINT)
    {
        $class = $this->getClassForNumber($number, $switchPoint);

        return '<span class="indicator '.$class.'">'.$number.'</span>';
    }

    public function priceFilter($price, $currency = "zł", $decimals = 2, $decPoint = '.', $thousandsSep = ' ')
    {
        $price = number_format($price, $decimals, $decPoint, $thousandsSep);
        $price = $price." ".$currency;

        return '<span class="price">'.$price.'</span>';
    }

    public function getClassForNumber($number, $switchPoint)
    {
        $class = self::CLASS_CHEAP;
        if(((float)$number) > $switchPoint) {
            $class = self::CLASS_EXPENSIVE;
        }

        return $class;
    }
}