<?php declare(strict_types=1);

use Chiiya\CodeStyle\CodeStyle;
use Rector\Core\Configuration\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

return static function (\Rector\Config\RectorConfig $config): void {
    $config->paths([
        app_path(),
        config_path(),
        lang_path(),
        base_path('tests'),
    ]);
    $config->skip([
        __DIR__.'/**/*/node_modules',
    ]);
    $config->importNames();
    $config->import(CodeStyle::RECTOR);
};
