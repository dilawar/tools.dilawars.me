<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/public',
        __DIR__ . '/tests',
    ])
    // uncomment to reach your current PHP version
    ->withPhpSets()
    ->withTypeCoverageLevel(30)
    ->withDeadCodeLevel(30)
    ->withCodeQualityLevel(30);
