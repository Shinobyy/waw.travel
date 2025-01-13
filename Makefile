# Makefile pour un projet Symfony

# Variables
CONSOLE = symfony
COMPOSER = composer

# Commandes
install:
	$(COMPOSER) install

update:
	$(COMPOSER) update

start:
	php bin/console tailwind:build --watch && $(CONSOLE) server:start -d

stop:
	$(CONSOLE) server:stop

cache-clear:
	$(CONSOLE) cache:clear

migrate:
	$(CONSOLE) doctrine:migrations:migrate

make-migration:
	$(CONSOLE) make:migration

fixtures-load:
	$(CONSOLE) doctrine:fixtures:load

test:
	php bin/phpunit

.PHONY: install update server stop-server cache-clear migrate make-migration fixtures-load test