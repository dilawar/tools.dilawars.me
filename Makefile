PHPSTAN:=./vendor/bin/phpstan --memory-limit=1G
PHPDOC:=podman run --rm -v $(PWD):/data docker.io/phpdoc/phpdoc:3
export COMPOSER_MEMORY_LIMIT:=-1

dev: install_dev
	ENVIRONMENT=testing php spark serve

install:
	composer install --no-dev

deploy: install db_migrate

db_migrate:
	./spark migrate
	./spark db:seed StatsSeeder

.PHONY: install

install_dev:
	composer install 

fix fmt:
	./vendor/bin/ecs --fix

lint: 
	$(PHPSTAN) analyze app

phpstan_update_baseline: 
	$(PHPSTAN) analyze app/ tests/ --generate-baseline

test:
	XDEBUG_MODE=coverage ./vendor/bin/phpunit

test_fail_fast:
	XDEBUG_MODE=coverage ./vendor/bin/phpunit --order-by=defects --stop-on-failure

test_group:
	XDEBUG_MODE=coverage ./vendor/bin/phpunit --group $(GROUP)

doc doc_docker:
	$(PHPDOC) run -d app -t docs

ci: install_dev
	$(MAKE) fmt
	$(MAKE) lint
