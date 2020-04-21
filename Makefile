.PHONY: fixer

fixer: .php_cs.dist
	php-cs-fixer --config=.php_cs.dist fix ./src