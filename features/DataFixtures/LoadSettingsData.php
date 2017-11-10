<?php

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Setting\Entity\Setting;

class LoadSettingsData implements OrderedFixtureInterface, FixtureInterface
{
    private $manager;

    public function getOrder()
    {
        return 10;
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->addSetting(Setting::SETTING_INDICATOR_CZCWK, Setting::SETTING_SECTION_INDICATORS, 22);
        $this->addSetting(Setting::SETTING_INDICATOR_CZ1Y, Setting::SETTING_SECTION_INDICATORS, 18.5);
        $this->addSetting(Setting::SETTING_INDICATOR_CZ7Y, Setting::SETTING_SECTION_INDICATORS, 23.5);

        $this->manager->flush();
    }

    private function addSetting($settingName, $settingSection, $settingValue)
    {
        $setting = Setting::create($settingName, $settingValue, $settingSection);

        $this->manager->persist($setting);
    }
}