# CLAUDE.md — Gold Lending Management System

## Проект

**Gold** — система управления кредитами, займами и финансовыми операциями.  
**GitHub:** `shakirena/gold`  
**Branch strategy:** `master` — production, `feature/{N}-{name}` — фичи, `hotfix/{N}-{name}` — хотфиксы.

---

## Стек

| Слой | Технология |
|------|-----------|
| Framework | Yii2 Basic (PHP 7.1+) |
| БД | MySQL 5.7+, dbname: `gold` |
| ORM | Yii2 ActiveRecord |
| Frontend | Bootstrap 3, jQuery, Kartik widgets |
| Тесты | Codeception 2.3 |
| Контейнер | Docker (yiisoftware/yii2-php:7.1-apache), порт 8000 |

---

## Структура модулей

```
gold/
├── config/          # web.php, console.php, db.php, params.php
├── controllers/     # Web-контроллеры (ClientController, CreditController, ...)
├── models/          # ActiveRecord модели + Search-модели
├── views/           # Yii2 Views (layouts + модульные папки)
├── widgets/         # Кастомные виджеты
├── assets/          # AssetBundle классы
├── commands/        # Console-контроллеры
├── docs/            # Документация (specs, arch, test-cases)
└── tests/           # Codeception тесты
    ├── unit/
    ├── functional/
    └── acceptance/
```

## Домены

| Контроллер | Модель | Назначение |
|-----------|--------|-----------|
| ClientController | Client | Клиенты |
| CreditController | Credit | Кредиты/займы |
| PaymentController | Payment | Платежи |
| FineController | Fine | Штрафы |
| CostsController | Costs | Расходы |
| GuarantorController | Guarantor | Поручители |
| KassaController | Kassa | Касса/транзакции |
| MonthController | — | Месячные отчёты |
| ProductsController | Products | Продукты/тарифы |
| StoreController | Store | Склад |
| TypeCostsController | TypeCosts | Типы расходов |
| UsersController | Users | Пользователи системы |

---

## Build & Test команды

```bash
# Установка зависимостей
composer install

# Запуск приложения через Docker
docker-compose up -d

# Все тесты
php vendor/bin/codecept run

# Unit тесты
php vendor/bin/codecept run unit

# Functional тесты
php vendor/bin/codecept run functional

# Один тест-файл
php vendor/bin/codecept run unit models/CreditTest.php

# Миграции
php yii migrate
php yii migrate/create create_table_name
php yii migrate/down  # откат

# Консоль Yii
php yii <command>/<action>
```

---

## Deploy

```bash
# Локальная разработка (OSPanel)
# Домен: gold (D:\OSPanel\domains\gold)

# Docker
docker-compose up -d
docker-compose down

# Staging (если настроен)
# TODO: настроить staging-среду
```

---

## Ролевая модель (RBAC)

| Роль | Доступ |
|------|--------|
| admin | Полный доступ ко всем функциям |
| manager | Управление кредитами, клиентами, платежами |
| cashier | Касса, платежи, просмотр |
| viewer | Только просмотр |

Реализовано через `app\models\Users` + `yii\web\User`.

---

## Coding Style

- **PHP**: PSR-2, Yii2 conventions
- **Модели**: ActiveRecord, правила валидации в `rules()`, labels в `attributeLabels()`
- **Контроллеры**: наследуют `yii\web\Controller`, actions — camelCase
- **Views**: PHP templates, используем Kartik widgets (GridView, Select2, DatePicker)
- **Миграции**: `php yii migrate/create`, класс в `console/migrations/`
- **Именование таблиц**: snake_case (клиенты → `clients`, кредиты → `credits`)
- **Именование классов**: PascalCase

---

## Важные ограничения

- PHP 7.1 — без union types, без arrow functions
- Yii2 (не Yii3) — используем старый API
- Bootstrap 3 (не 4/5) — классы `.col-md-*`, `.btn-default` и т.д.
- Не используем namespace кроме `app\*`
- `gh` CLI требуется для GitHub/Kanban интеграции
