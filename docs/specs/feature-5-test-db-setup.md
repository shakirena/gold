# Feature #5: Настроить тестовую БД yii2_basic_tests

**Issue:** #5
**Status:** Ready for Dev (fast-track)
**Date:** 2026-04-16
**Author:** analysis-lead (fast-track)

---

## Описание

Yii2 Codeception module с `part: [orm]` при инициализации suite пытается подключиться к БД, указанной в `config/test_db.php`. БД `yii2_basic_tests` не существует — всё юнит-тесты падают с `SQLSTATE[HY000] [1049] Unknown database 'yii2_basic_tests'`, включая те, которые не используют БД (UserTest, LoginFormTest, ContactFormTest).

---

## Functional Requirements

| ID | Требование |
|----|-----------|
| FR-1 | БД `yii2_basic_tests` создана в MySQL с полной схемой (структура таблиц из `gold`). |
| FR-2 | `php vendor/bin/codecept run unit` завершается без ошибок `Unknown database`. |
| FR-3 | Существующие чистые юнит-тесты (FineValidationTest, CreditDebtTest) продолжают проходить. |

## Non-Functional Requirements

| ID | Требование |
|----|-----------|
| NFR-1 | Тестовая БД не содержит production-данных. |
| NFR-2 | `config/test_db.php` не требует изменений (имя `yii2_basic_tests` остаётся). |

---

## Acceptance Criteria

**Given** MySQL запущен в OSPanel  
**When** запускается `php vendor/bin/codecept run unit`  
**Then** ошибок `Unknown database 'yii2_basic_tests'` нет; UserTest, LoginFormTest, ContactFormTest выполняются без DB-ошибок

---

## Out of Scope

- Fixtures с production-данными
- Изменение имени тестовой БД
- Настройка CI/CD пайплайна
