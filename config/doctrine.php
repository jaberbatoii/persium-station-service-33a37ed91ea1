<?php

declare(strict_types = 1);

return [
        'mapping_path' => env('DOCTRINE_MAPPING_PATH', app()->path().'/Infrastructures/Persistency/Doctrine/Mapping'),
];
