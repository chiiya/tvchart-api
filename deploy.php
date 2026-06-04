<?php

declare(strict_types=1);

namespace Deployer;

require 'recipe/laravel.php';

set('application', 'tvchart-api');
set('repository', 'git@github.com:chiiya/tvchart-api.git');
set('keep_releases', 5);
set('forward_agent', true);

host('production')
    ->setHostname('tvchart.chiiya.moe')
    ->setRemoteUser('deploy')
    ->setDeployPath('/var/www/tvchart.chiiya.moe');

task('php-fpm:reload', function (): void {
    run('sudo systemctl reload php8.4-fpm');
});

desc('Cache Filament components and Blade icons');
task('artisan:filament:optimize', artisan('filament:optimize'));

after('artisan:optimize', 'artisan:filament:optimize');
after('deploy:publish', 'php-fpm:reload');
after('deploy:publish', 'artisan:horizon:terminate');
after('deploy:failed', 'deploy:unlock');
