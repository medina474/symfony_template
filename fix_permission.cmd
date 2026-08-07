@echo off
docker compose exec php chown -R 1000:1000 .

echo Ok
