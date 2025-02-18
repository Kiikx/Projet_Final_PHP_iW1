.PHONY: up down restart logs migrate db-shell reset-db

# Construire les conteneurs
build:
	docker-compose build

# Démarrer les conteneurs
up:
	docker-compose up -d

# Arrêter les conteneurs
down:
	docker-compose down

# Redémarrer les conteneurs
restart: down up

# Voir les logs
logs:
	docker-compose logs -f

# Appliquer les migrations
migrate:
	docker-compose exec php php db/migrate.php


# Rollback des migrations
rollback:
	docker-compose exec php php db/rollback.php

# Rollback de toutes les migrations
rollback-all:
	docker-compose exec php php db/rollback.php 9999

# Accéder au shell MySQL (via le conteneur)
db-shell:
	docker-compose exec mariadb mysql -uuser -ppassword database

# Réinitialiser la BDD (⚠️ supprime toutes les données !)
reset-db: down
	rm -rf database
	docker-compose up -d
