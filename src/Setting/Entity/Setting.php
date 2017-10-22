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

    const SETTING_SECTION_INDICATORS = 'sec_ind';

    /**
     * @var string|null
     * @ORM\Column(name="value", type="string", nullable=true)
     */
    protected $value;
}