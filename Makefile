PHPSTAN:=./vendor/bin/phpstan --memory-limit=1G
PHPDOC:=podman run --rm -v $(PWD):/data docker.io/phpdoc/phpdoc:3
export COMPOSER_MEMORY_LIMIT:=-1

dev:
	ENVIRONMENT=testing php spark serve

install:
	sudo pecl install parallel
	composer update && composer install

bootstrap:
	curl https://frankenphp.dev/install.sh | sh
	sudo mv frankenphp /usr/local/bin/
	sudo chmod +x /usr/local/bin/frankenphp

deploy frankenphp: install
	sudo /usr/local/bin/frankenphp start

.PHONY: install
install:
	composer install --no-dev

fix fmt:
	composer format 

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

ci:
	$(MAKE) install
	$(MAKE) lint
