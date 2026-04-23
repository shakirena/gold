# Architecture: Feature #5 — Настроить тестовую БД yii2_basic_tests

**Date:** 2026-04-16

---

## Root Cause

`tests/unit.suite.yml` подключает Yii2 Codeception module с `part: [orm]`. При инициализации suite модуль поднимает полное Yii2 приложение через `config/test.php`, которое подключает `config/test_db.php`. Там указан `dbname=yii2_basic_tests`, которого нет в MySQL — ошибка происходит до запуска любого теста.

## Решение

Создать `yii2_basic_tests` с полной схемой таблиц из `gold`. Данные не нужны: `User`, `LoginForm`, `ContactForm` используют статический массив или file-based mailer.

### Схема операций

```
mysqldump gold (structure only)
  → CREATE DATABASE yii2_basic_tests
  → применить схему
```

## Затронутые файлы

| Файл | Изменение |
|------|-----------|
| `config/test_db.php` | Не меняется |
| `tests/unit.suite.yml` | Не меняется |
| MySQL: `yii2_basic_tests` | Создать новую БД |

## Нет изменений в коде приложения.
