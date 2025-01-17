app:
	@docker compose run --rm app sh

setup:
	@docker compose run --rm app composer install

phpstan:
	@docker compose run --rm app php ./vendor/phpstan/phpstan/phpstan analyse --memory-limit 1G

phpunit:
	@docker compose run --rm app php ./vendor/phpunit/phpunit/phpunit --no-coverage

coverage:
	@docker compose run --rm app php ./vendor/phpunit/phpunit/phpunit