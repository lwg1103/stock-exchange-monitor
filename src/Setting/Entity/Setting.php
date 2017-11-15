<?php
namespace Setting\Entity;

use Craue\ConfigBundle\Entity\BaseSetting;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Craue\ConfigBundle\Repository\SettingRepository")
 * @ORM\Table(name="settings")
 */
class Setting extends BaseSetting {

    const SETTING_INDICATOR_CZCWK = 'ind_czcwk';
    const SETTING_INDICATOR_CZ1Y = 'ind_cz1Y';
    const SETTING_INDICATOR_CZ7Y = 'ind_cz7Y';
    const SETTING_INDICATOR_NTB_RATE = 'ind_ntb_rate';

    const SETTING_SECTION_INDICATORS = 'sec_ind';

    /**
     * @var string|null
     * @ORM\Column(name="value", type="string", nullable=true)
     */
    protected $value;
}