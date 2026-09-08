CONTAINER_NAME=dev-wargames2-php
# The application's own database user, which needs rights on the test database too.
DB_USER=eharo
CONTAINER_CMD=docker exec -it $(CONTAINER_NAME)
COMPOSER_CMD=$(CONTAINER_CMD) composer
PHP_CMD=$(CONTAINER_CMD) php
PHPUNIT_CMD=$(PHP_CMD) bin/phpunit --colors=always
COMPOSER_FILE=docker-compose.yml

CYAN=\033[0;36m
GREEN=\033[0;32m
BOLD=\033[1m
NC=\033[0m

composer-install:
	$(COMPOSER_CMD) install --optimize-autoloader

composer-update:
	$(COMPOSER_CMD) update

composer-require:
	@read -p "Enter the library to require (e.g. symfony/var-dumper): " library; \
	$(COMPOSER_CMD) require $$library

composer-dump-autoload:
	$(COMPOSER_CMD) dump-autoload

shell:
	$(CONTAINER_CMD) /bin/bash

build:
	docker-compose up -d --build
	@$(MAKE) composer-install

clean:
	docker-compose down -v

stop:
	docker-compose stop

start:
	docker-compose start

logs:
	docker-compose logs -f $(CONTAINER_NAME)

cache-clear:
	$(PHP_CMD) bin/console cache:clear

migration-status:
	$(PHP_CMD) ./vendor/bin/doctrine-migrations status

migration-generate:
	$(PHP_CMD) ./vendor/bin/doctrine-migrations generate $(filter-out $@,$(MAKECMDGLOBALS))

migration-migrate:
	$(PHP_CMD) ./vendor/bin/doctrine-migrations migrate $(filter-out $@,$(MAKECMDGLOBALS))

migration-reset:
	$(PHP_CMD) ./vendor/bin/doctrine-migrations migrate first --no-interaction

migration-rollback:
	$(PHP_CMD) ./vendor/bin/doctrine-migrations execute $(version) --down

# The two baseline accounts and the official maps. Idempotent, so it belongs after every
# migrate rather than being run once by hand - see SeedBaselineDataConsoleCommand.
seed:
	$(PHP_CMD) bin/console app:seed

# What a fresh checkout needs to become a working application.
setup: migration-migrate seed

# Stripe cannot reach a container on your laptop, so the CLI holds a connection open and
# replays events onto the local API. Prints the signing secret on startup - copy it into
# STRIPE_WEBHOOK_SECRET in .env.local, because it changes every time the container is recreated.
#
# The key comes from .env.local, which is where Symfony already keeps it - compose only reads
# .env by default, which is why both files are passed. Without the key the CLI container starts,
# prints "You have not configured API keys yet" and exits, and nothing else notices: payments
# then complete in Stripe and never reach the API, so plans appear not to change.
stripe-listen:
	@test -f .env.local || (echo "No .env.local: the Stripe key lives there, not in .env"; exit 1)
	docker compose --env-file .env --env-file .env.local up -d dev-stripe
	@sleep 5
	@docker logs dev-wargames-stripe 2>&1 | grep -m1 'signing secret' || \
		echo "No signing secret yet - check 'docker logs dev-wargames-stripe'"

stripe-logs:
	docker logs -f dev-wargames-stripe

.PHONY: cs-fixer
cs-fixer:
	@read -p "Route to fix: " route; \
	$(PHP_CMD) ./vendor/bin/php-cs-fixer fix --verbose $$route

.PHONY: cs-fixer-all
cs-fixer-all:
	$(PHP_CMD) ./vendor/bin/php-cs-fixer fix --verbose src/

.PHONY: cs-fixer-precommit
cs-fixer-precommit:
	@FILES=$$(git diff --name-only --diff-filter=d | grep -E '\.php');
	if [ -z $$FILES ]; then \
  		echo "No PHP files to fix"; \
  		exit 0; \
	else \
	  	for FILE in $$FILES; do \
	  	  	$(PHP_CMD) ./vendor/bin/php-cs-fixer fix --verbose $$FILE; \
		done \
	fi \

debug-command:
	$(PHP_CMD) bin/console debug:container --tag=app.command_handler

debug-router:
	$(PHP_CMD) bin/console debug:router

.PHONY: test-db
test-db:
	@printf "$(CYAN)$(BOLD)▶ Creating the test database$(NC)\n"
	@docker exec dev-wargames-db mysql -uroot -proot -e \
		"CREATE DATABASE IF NOT EXISTS wargames_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; \
		 GRANT ALL PRIVILEGES ON wargames_test.* TO '$(DB_USER)'@'%'; FLUSH PRIVILEGES"
	@docker exec -e APP_ENV=test $(CONTAINER_NAME) ./vendor/bin/doctrine-migrations migrate \
		--no-interaction --allow-no-migration
	@printf "$(GREEN)Done. The suites no longer touch your development data.$(NC)\n"

.PHONY: test test-unit test-integration test-acceptance test-scoped

test-unit:
	@printf "$(CYAN)$(BOLD)▶ Unit tests$(NC)\n"
	@$(PHPUNIT_CMD) --testsuite=unit --testdox

test-integration:
	@printf "$(CYAN)$(BOLD)▶ Integration tests$(NC)\n"
	@$(PHPUNIT_CMD) --testsuite=integration --testdox

test-acceptance:
	@printf "$(CYAN)$(BOLD)▶ Acceptance tests$(NC)\n"
	@$(PHPUNIT_CMD) --testsuite=acceptance --testdox

# Runs the three suites one after another, each with its own banner, stopping at the first failure.
test: test-unit test-integration test-acceptance
	@printf "$(GREEN)$(BOLD)✔ All suites passed$(NC)\n"

# Scope a run to one context/module, e.g.:
#   make test-scoped SUITE=unit CONTEXT=Platform
#   make test-scoped SUITE=unit CONTEXT=Platform MODULE=Authentication
#   make test-scoped SUITE=integration CONTEXT=Platform MODULE=Campaigns
SUITE_DIR = $(shell echo $(SUITE) | sed 's/^./\U&/')
test-scoped:
	@if [ -z "$(SUITE)" ] || [ -z "$(CONTEXT)" ]; then \
		printf "Usage: make test-scoped SUITE=unit|integration|acceptance CONTEXT=<Context> [MODULE=<Module>]\n"; \
		exit 1; \
	fi
	@printf "$(CYAN)$(BOLD)▶ $(SUITE_DIR) tests — $(CONTEXT)$(if $(MODULE),/$(MODULE))$(NC)\n"
	@$(PHPUNIT_CMD) --testdox tests/$(SUITE_DIR)/$(CONTEXT)$(if $(MODULE),/$(MODULE))
