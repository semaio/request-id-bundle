.PHONY: clean cs-check cs-fix phpstan test test-functional test-unit

clean:
	rm -rf tests/Functional/app/tmp/*

cs-check:
	php vendor/bin/ecs check

cs-fix:
	php vendor/bin/ecs check --fix

phpstan:
	php vendor/bin/phpstan

test-functional: clean
	php vendor/bin/phpunit --testsuite functional

test-unit:
	php vendor/bin/phpunit --testsuite unit

test: clean
	php vendor/bin/phpunit
