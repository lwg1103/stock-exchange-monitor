<?php
namespace Dividend\Parser\Stockwatch;

use Company\Entity\Company;
use Dividend\Entity\Dividend\State;

class StockwatchRowParser
{

    const CLEAR_OLD_DATA = false;
    
    const DIVIDEND_STATUS_PAID_INDICATOR = 'wypłacona';
    const DIVIDEND_STATUS_PASSED_INDICATOR = 'uchwalona';
    const DIVIDEND_STATUS_PROPOSAL_INDICATOR = 'proponowana';

    var $rowString = '';
    
    /**
     * StockwatchRowParser constructor.
     * $row string
     */
    public function __construct($row)
    {
        $this->rowString = $row;
    }

    public function extractDataFromRow() {
        // wygląda na skomplikowany ale wyciąga po prostu zawartosć każdej komórki
        $re = '/<td[^>]*><strong><a.*>(.*)<\/a>.*<td[^>]*>od&nbsp;(.*)<br\/>do&nbsp;(.*)<\/td>.*<td[^>]*>(.*)<\/td>.*<td[^>]*>(.*)<\/td>.*<td[^>]*>(.*)<\/td>.*<td[^>]*>(.*)<\/td>.*<td[^>]*>(.*)<\/td>.*<td[^>]*>(.*)<\/td>/si';
        preg_match_all($re, $this->rowString, $matches, PREG_SET_ORDER, 0);
        
        
        if($match = $matches[0]) {
            if (count($match) == 10) {
                //first element contains whole row string
                array_shift($match);
                
                $company = $match[0];
                $periodFrom = $match[1];
                $periodTo = $match[2];
                $value = $this->getValueFromString($match[3]);
                $currency = 'PLN';
                $rate = $this->getFloatFromString($match[4]);
                $paymentDate = $this->getPaymentDateFromString($match[7]);
                $status = $this->getStatusFromString($match[7]);
                $agmDate = $match[8];

                return array(
                    'company' => $company,
                    'period_from' => $periodFrom,
                    'period_to' => $periodTo,
                    'value' => $value,//cena będzie przetrzymywana w groszach
                    'currency' => 'PLN',
                    'rate' => $rate,
                    'state' => $status,
                    'payment_date' => $paymentDate,
                    'agm_date' => $agmDate,
                );
            }
        }
    }
    
    private function parseStatus($stringStatus) {
        $stringStatus = trim($stringStatus);
        $re = '/(<span[^>]*>!<\/span>)?(<a[^>]*>)?([^<]*)(<\/a>)?[^>]*<div class="stcm">([0-9-]+)?<\/div>/is';
        
        preg_match($re, $stringStatus, $status, PREG_OFFSET_CAPTURE, 0);
        
        return $status;
    }
    
    private function getPaymentDateFromString($stringDate) {
        $status = $this->parseStatus($stringDate);
        
        $payment_date = '';
        if(count($status) >= 6) {
            $payment_date = $status[5][0];
        }
        
        return $payment_date;
    }
    
    private function getStatusFromString($stringStatus) {
        $status = $this->parseStatus($stringStatus);
        
        if(!count($status) || count($status) < 4) {
            return '';
        }
        
        $status = trim($status[3][0]);
        return $this->parseDividendStatus($status);
    }
    
    private function getValueFromString($stringValue) {
        $stringValue = $this->getFloatFromString($stringValue);
        return ((float)$stringValue*100);
    }
    
    private function getFloatFromString($stringValue) {
        return str_replace(array('%', ','), array('', '.'), $stringValue);
    }
    
    private function parseDividendStatus($data) {
        if(strpos($data, self::DIVIDEND_STATUS_PAID_INDICATOR) !== false) {
            return State::PAID;
        }
        if(strpos($data, self::DIVIDEND_STATUS_PASSED_INDICATOR) !== false) {
            return State::PASSED;
        }
        if(strpos($data, self::DIVIDEND_STATUS_PROPOSAL_INDICATOR) !== false) {
            return State::PROPOSAL;
        }
        return '';//$data;
    }
}