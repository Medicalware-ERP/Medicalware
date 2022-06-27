build:
	$(MAKE) prepare-test
	$(MAKE) tests

.PHONY: tests
tests:
	php bin/phpunit

prepare-dev:
	npm install
	composer install --prefer-dist
	php bin/console doctrine:database:drop --if-exists -f --env=dev
	php bin/console doctrine:database:create --env=dev
	php bin/console doctrine:schema:update -f --env=dev
	php bin/console doctrine:fixtures:load -n --env=dev
	php bin/console app:data:init
	php bin/console app:generate:resources

prepare-test:
	npm install
	npm run dev
	composer install --prefer-dist
	php bin/console cache:clear --env=test
	php bin/console doctrine:database:drop --if-exists -f --env=test
	php bin/console doctrine:database:create --env=test
	php bin/console doctrine:schema:update -f --env=test
	php bin/console doctrine:fixtures:load -n --env=test

resetDbDev:
	php bin/console doctrine:database:drop --if-exists -f --env=dev
	php bin/console doctrine:database:create --env=dev
	php bin/console doctrine:schema:update -f --env=dev
	php bin/console doctrine:fixtures:load -n --env=dev

dumpRoutes:
	php bin/console fos:js-routing:dump --format=json --target=assets/js/fos_js_routes.json