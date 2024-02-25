<?php

namespace Persium\Station\Dictionary;

class AQIDictionary
{
    const DAQI = 'daqi';
    const CAQI = 'caqi';
    const USAQI = 'usaqi';

    const DAQI_NAME = 'Daily Air Quality Index';
    const CAQI_NAME = 'Common Air Quality Index';
    const USAQI_NAME = 'US AQI';

    const TYPE_DAQI = 1;
    const TYPE_CAQI = 2;
    const TYPE_USAQI = 3;

    const NAME_MAP = [
        self::DAQI => self::TYPE_DAQI,
        self::CAQI => self::TYPE_CAQI,
        self::USAQI => self::TYPE_USAQI,
    ];
}
