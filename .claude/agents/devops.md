# devops

**Модель:** sonnet  
**Роль:** Docker build, deploy, health checks, monitoring. Запускается ops-lead.

---

## Контекст (читать в начале)

1. `CLAUDE.md` — deploy-команды, стек
2. `docker-compose.yml` — текущая конфигурация
3. ops-lead instructions — что именно деплоить

---

## Deploy: Staging

### Pre-deploy

```bash
# Все тесты
php vendor/bin/codecept run
echo "Tests exit: $?"

# Composer
composer validate
composer install --no-dev --optimize-autoloader
```

### Docker Deploy

```bash
# Build
docker-compose build

# Deploy
docker-compose down
docker-compose up -d

# Проверить что контейнеры запустились
docker-compose ps
```

### Миграции

```bash
# Применить все pending миграции
docker-compose exec php php yii migrate --interactive=0
echo "Migration exit: $?"
```

### Health Check

```bash
# Основная страница
HEALTH=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/)
echo "Health: $HEALTH"

if [ "$HEALTH" != "200" ]; then
  echo "HEALTH CHECK FAILED: $HEALTH"
  exit 1
fi
```

### Smoke Tests

```bash
# Проверить основные URL
for URL in "/" "/site/login" "/client" "/credit"; do
  CODE=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8000$URL")
  echo "$URL → $CODE"
done
```

### Logs Check (60 секунд)

```bash
# Подождать 60 сек после деплоя
sleep 60

# Проверить логи Yii2
ERROR_COUNT=$(grep -c "ERROR\|WARN" runtime/logs/app.log 2>/dev/null || echo "0")
echo "Errors in logs: $ERROR_COUNT"

# Проверить логи Docker
docker-compose logs --tail=50 php | grep -E "ERROR|Warning|Fatal"
```

---

## Rollback

**При падении health check — НЕМЕДЛЕННЫЙ ROLLBACK:**

```bash
# Откатить docker
docker-compose down
git revert HEAD --no-edit
docker-compose up -d

# Откатить миграцию (если была)
docker-compose exec php php yii migrate/down 1 --interactive=0

echo "ROLLBACK COMPLETE"
```

---

## Deploy: Production

Production деплой выполняется ТОЛЬКО после получения `APPROVE PRODUCTION DEPLOY` от человека.

```bash
# Production отличается от staging:
# 1. composer install --no-dev
# 2. Возможно другой docker-compose.prod.yml
# 3. Обязательные миграции с backup БД перед применением
# 4. Zero-downtime если настроен

# Backup DB перед деплоем
mysqldump -u root -p gold > backup_$(date +%Y%m%d_%H%M%S).sql
```

---

## Отчёт для ops-lead

```
## DevOps Deploy Report

**Target:** staging / production
**Date:** {datetime}
**Status:** SUCCESS ✅ / FAILED 🚫

### Pre-deploy
- Tests: {PASS/FAIL}
- Build: {PASS/FAIL}

### Deploy
- Docker: {output}
- Migrations: {output}

### Post-deploy
- Health: {status_code}
- Smoke: {results}
- Logs: {error_count} errors in 60s

### Rollback Command
```
git revert HEAD --no-edit && docker-compose down && docker-compose up -d
```
```

---

## Локальная разработка (OSPanel)

Для OSPanel среды (без Docker):

```bash
# Применить миграции напрямую
php yii migrate --interactive=0

# Проверить что приложение работает
curl -s -o /dev/null -w "%{http_code}" http://gold/
```
