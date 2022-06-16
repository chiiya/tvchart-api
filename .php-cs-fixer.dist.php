<?php declare(strict_types=1);

use Chiiya\CodeStyle\Config;
use PhpCsFixer\Finder;
use PhpCsFixerCustomFixers\Fixer\CommentedOutFunctionFixer;

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

return (new Config)
    ->setFinder(
        Finder::create()
            ->in(app_path())
            ->in(config_path())
            ->in(lang_path())
            ->in(base_path('tests'))
            ->exclude('node_modules'),
    )
    ->setRules([
        '@Chiiya' => true,
        '@Chiiya:risky' => true,
        CommentedOutFunctionFixer::name() => [
            'functions' => ['dd', 'dump', 'ini_set', 'print_r', 'var_dump', 'var_export'],
        ],
    ])
    ->setRiskyAllowed(true);
