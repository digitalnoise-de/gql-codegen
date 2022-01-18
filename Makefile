.PHONY: it
it: coding-standards analysis tests

.PHONY: code-coverage
code-coverage: dependencies
	XDEBUG_MODE=coverage tools/phpunit --coverage-text

.PHONY: analysis
analysis: dependencies
	tools/psalm --no-cache --output-format=phpstorm

.PHONY: coding-standards
coding-standards: dependencies
	tools/php-cs-fixer fix --config=.php-cs-fixer.dist.php --diff --verbose

.PHONY: mutation-tests
mutation-tests: dependencies
	XDEBUG_MODE=coverage ./vendor/bin/roave-infection-static-analysis-plugin --min-covered-msi=80 --min-msi=80 --logger-html=reports/infection.html

.PHONY: tests
tests: dependencies
	tools/phpunit

dependencies: vendor phive

vendor: composer.json composer.lock
	composer validate
	composer install

phive: phive.xml
	phive install
