<?php

namespace App\DataFixtures;

use App\Entity\Setting;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class SettingFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
       $setting = $this->createSetting();

       $manager->persist($setting);

        $manager->flush();
    }

    private function createSetting(): Setting
    {
        $setting = new Setting();

        $setting->setEmail("ancien-footballeur@gmail.com");
        $setting->setPhone("06 06 06 06 06");

        return $setting;
    }
}
