# Makefile for Symfony Project

#php bin/console doctrine:database:create
#php bin/console doctrine:migrations:migrate
#php bin/console doctrine:fixtures:load

# Define commands
APP=php bin/console

.PHONY: all create-db migrate load-fixtures

all: create-db migrate load-fixtures

create-db:
	@echo "Creating the database..."
	$(APP) doctrine:database:create --if-not-exists

migrate:
	@echo "Running migrations..."
	$(APP) doctrine:migrations:migrate --no-interaction

load-fixtures:
	@echo "Loading fixtures..."
	$(APP) doctrine:fixtures:load --no-interaction