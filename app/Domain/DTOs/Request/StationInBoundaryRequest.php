<?php

namespace Persium\Station\Domain\DTOs\Request;

use Persium\Station\Dictionary\AQIDictionary;
use Psr\Http\Message\ServerRequestInterface;

class StationInBoundaryRequest
{
    public function __construct(
        public float $lat1,
        public float $lng1,
        public float $lat2,
        public float $lng2,
        public float $lat3,
        public float $lng3,
        public float $lat4,
        public float $lng4,
        public int $aqi_type,
    )
    {
    }



    public static function createFromRequest(ServerRequestInterface $request): self
    {
        //validate data
        $params = $request->getQueryParams();
        if (!isset($params['lat1'], $params['lng1'], $params['lat2'], $params['lng2'], $params['lat3'], $params['lng3'], $params['lat4'], $params['lng4'])){
            throw new \InvalidArgumentException('Missing latitude/longitude params.');
        }
        if (!is_numeric($params['lat1']) || !is_numeric($params['lng1'])
            || !is_numeric($params['lat2']) || !is_numeric($params['lng2'])
            || !is_numeric($params['lat3']) || !is_numeric($params['lng3'])
            || !is_numeric($params['lat4']) || !is_numeric($params['lng4'])){
            throw new \InvalidArgumentException('Latitude/longitude params must be numeric.');
        }

        $aqi_type = $params['aqi_type'] ?? AQIDictionary::DAQI;
        if (!in_array($aqi_type, [AQIDictionary::DAQI, AQIDictionary::CAQI, AQIDictionary::USAQI]) || !isset(AQIDictionary::NAME_MAP[$aqi_type])){
            throw new \InvalidArgumentException('AQI type is invalid.');
        }

        $lat1 = (float)$params['lat1'];
        $lng1 = (float)$params['lng1'];
        $lat2 = (float)$params['lat2'];
        $lng2 = (float)$params['lng2'];
        $lat3 = (float)$params['lat3'];
        $lng3 = (float)$params['lng3'];
        $lat4 = (float)$params['lat4'];
        $lng4 = (float)$params['lng4'];

        if (!self::isRectangle($lat1, $lng1, $lat2, $lng2, $lat3, $lng3, $lat4, $lng4)){
            throw new \InvalidArgumentException('The four points do not form a rectangle.');
        }

        return new self(
            $lat1,
            $lng1,
            $lat2,
            $lng2,
            $lat3,
            $lng3,
            $lat4,
            $lng4,
            AQIDictionary::NAME_MAP[$aqi_type],
        );
    }

    private static function isRectangle(
        float $lat1, float $lng1,
        float $lat2, float $lng2,
        float $lat3, float $lng3,
        float $lat4, float $lng4,
    ): bool
    {
        $epsilon = 1E-9;
        $cx = ($lng1 + $lng2 + $lng3 + $lng4) / 4;
        $cy = ($lat1 + $lat2 + $lat3 + $lat4) / 4;

        $dd1 = pow($cx - $lng1, 2) + pow($cy - $lat1, 2);
        $dd2 = pow($cx - $lng2, 2) + pow($cy - $lat2, 2);
        $dd3 = pow($cx - $lng3, 2) + pow($cy - $lat3, 2);
        $dd4 = pow($cx - $lng4, 2) + pow($cy - $lat4, 2);

        return abs($dd1 - $dd2) < $epsilon
            && abs($dd1 - $dd3) < $epsilon
            && abs($dd1 - $dd4) < $epsilon;
    }
}
