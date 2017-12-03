<?php
namespace Setting\Entity;

use Craue\ConfigBundle\Entity\BaseSetting;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Craue\ConfigBundle\Repository\SettingRepository")
 * @ORM\Table(name="settings")
 */
class Setting extends BaseSetting {

    /** indicators */
    const SETTING_SECTION_INDICATORS = 'sec_ind';
    
    const SETTING_INDICATOR_CZCWK = 'ind_czcwk';
    const SETTING_INDICATOR_CZ1Y = 'ind_cz1Y';
    const SETTING_INDICATOR_CZ7Y = 'ind_cz7Y';
    
    /** market parameters */
    const SETTING_SECTION_MARKET_PARAMETERS = 'sec_market_parameters';
    
    const SETTING_PARAMETER_NTB_RATE = 'ind_ntb_rate';

    /**
     * @var string|null
     * @ORM\Column(name="value", type="string", nullable=true)
     */
    protected $value;
}