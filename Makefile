.PHONY: fixer fix

fixer: .php_cs.dist
	php-cs-fixer --config=.php_cs.dist fix ./src
	php-cs-fixer --config=.php_cs.dist fix ./tests

fix:
	phpcbf --standard=PSR12 ./src || true
	phpcbf --standard=PSR12 ./tests || true