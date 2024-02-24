<?php
namespace App\Trait;

    trait TimeZoneTrait
    {
        protected function changeTimeZone(mixed $timeZoneId): void
        {
            //Code source de php
            \date_default_timezone_set($timeZoneId);
        }

    }