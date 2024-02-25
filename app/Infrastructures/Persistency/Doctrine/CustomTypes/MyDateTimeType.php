<?php

namespace Persium\Station\Infrastructures\Persistency\Doctrine\CustomTypes;

use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class MyDateTimeType extends DateTimeType
{
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $dateTime = parent::convertToPHPValue($value, $platform);

        if ( ! $dateTime) {
            return $dateTime;
        }

        $val = new MyDateTime('@' . $dateTime->format('U'));
        $val->setTimezone($dateTime->getTimezone());
        return $val;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    public function getName(): string
    {
        return 'mydatetime';
    }
}
