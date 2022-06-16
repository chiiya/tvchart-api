<?php declare(strict_types=1);

use Chiiya\CodeStyle\CodeStyle;
use Symplify\EasyCodingStandard\Config\ECSConfig;

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

return static function (ECSConfig $config): void {
    $config->import(CodeStyle::ECS);
    $config->paths([
        app_path(),
        config_path(),
        lang_path(),
        base_path('tests'),
    ]);
};
