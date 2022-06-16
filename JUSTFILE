set dotenv-load := false
PHPUNIT := 'vendor/bin/phpunit -d xdebug.max_nesting_level=250 -d memory_limit=1024M --coverage-html reports/'

# [DDEV] Initial project setup
setup:
	cp .env.example .env
	cd public && ln -s ../storage/app/public storage
	ddev start
	ddev composer install
	ddev exec "php artisan key:generate"
	ddev exec "php artisan migrate:fresh"
	ddev exec "php artisan module:seed"
	echo '' | ddev import-db --target-db=test_db
	ddev exec "php artisan vendor:publish --tag=telescope-assets --force"

# [DDEV] Run the application (after initial setup)
up:
	ddev start
	ddev launch /admin

# [DDEV] Stop the application
down:
	ddev stop

# [DDEV] Enter webserver bash
@ssh:
	ddev ssh

# [DDEV] Lint files
@lint:
	ddev exec "./vendor/bin/ecs check --fix"
	ddev exec "./vendor/bin/php-cs-fixer fix"
	ddev exec "./vendor/bin/rector process"
	ddev exec "./vendor/bin/tlint lint"

# [DDEV] Check code quality
@quality:
	ddev exec "./vendor/bin/phpstan analyse --memory-limit=2G"

# [DDEV] Run unit and integration tests
@test:
	echo "Running unit and integration tests"; \
	ddev exec {{PHPUNIT}}

# [DDEV] Run tests and create code-coverage report
@coverage:
	echo "Running unit and integration tests"; \
	echo "Once completed, the generated code coverage report can be found under ./reports)"; \
	ddev xdebug;\
	ddev exec XDEBUG_MODE=coverage {{PHPUNIT}};\
	ddev xdebug off
	xdg-open reports/index.html

# [DDEV] Prepare branch for commit
@prepare: lint quality
	echo "All checks completed"

# [DDEV] Refresh testing snapshot
@dump:
	ddev exec php artisan snapshot:create temp
	ddev exec "php artisan migrate:fresh"
	ddev exec php artisan db:seed --class="App\\\Domain\\\Database\\\Seeders\\\TestSeeder"
	ddev exec php artisan snapshot:create domain
	ddev exec php artisan snapshot:load temp
	ddev exec php artisan snapshot:delete temp

# [DDEV] Launch PHPMyAdmin
@db:
	ddev launch -p

# [DDEV] List application commands
@list:
	ddev exec php artisan list tvchart
