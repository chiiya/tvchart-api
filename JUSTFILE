set dotenv-load := false

# default recipe to display help information
default:
  @just --list

# Initial project setup
setup:
	cp -n .env.example .env
	@test -d public/storage || (cd public && ln -s ../storage/app/public storage)
	composer install
	php artisan key:generate
	php artisan migrate:fresh
	php artisan module:seed
	php ./vendor/bin/grumphp git:init

# Lint files
@lint:
	./vendor/bin/ecs check --fix
	./vendor/bin/php-cs-fixer fix
	./vendor/bin/rector process
	./vendor/bin/tlint lint

# Check code quality
@quality:
	./vendor/bin/phpstan analyse --memory-limit=2G

# Run unit and integration tests
@test:
	echo "Running unit and integration tests"; \
	php artisan test

# Run tests and create code-coverage report with Xdebug
@coverage:
	echo "Running unit and integration tests with coverage"; \
	php artisan test
