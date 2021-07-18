<?php

namespace App\Types\Doctrine;

use App\Types\Date;
use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateType;

class UTCDateType extends DateType {
    private static ?DateTimeZone $utc = null;

    /**
     * {@inheritdoc}
     *
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string {
        if ($value instanceof \DateTime) {
            $value->setTimezone(self::getUtc());
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    /**
     * {@inheritdoc}
     *
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?DateTime {
        if (null === $value) {
            return $value;
        }

        $val = Date::createFromFormat('!'.$platform->getDateFormatString(), $value, self::getUtc());
        if (!$val) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateFormatString()
            );
        }

        return $val;
    }

    private static function getUtc(): DateTimeZone {
        return self::$utc ?: self::$utc = new DateTimeZone('UTC');
    }
}
