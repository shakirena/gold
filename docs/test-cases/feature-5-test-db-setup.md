# Test Cases: Feature #5 — Настроить тестовую БД yii2_basic_tests

**Issue:** #5
**Type:** type:tech-debt
**Date:** 2026-04-16
**QA Agent:** qa-lead

---

## Acceptance Criteria

**AC-1:** Given настроена тестовая БД; When запускается `php vendor/bin/codecept run unit`; Then все тесты проходят без ошибок `Unknown database 'yii2_basic_tests'`.

---

## Test Cases

### TC-5-01: Unit suite запускается без DB-ошибок

| Поле | Значение |
|------|---------|
| **ID** | TC-5-01 |
| **Priority** | Critical |
| **AC** | AC-1 |
| **Type** | Integration (DB) |

**Given:** `yii2_basic_tests` создана, схема скопирована из `gold`, фикстуры применены  
**When:** `php vendor/bin/codecept run unit`  
**Then:** выход `OK (N tests, M assertions)` — ни одного `Unknown database`

**Verified by:** ручной запуск suite, вывод `OK (24 tests, 47 assertions)`

---

### TC-5-02: LoginFormTest::testLoginCorrect проходит

| Поле | Значение |
|------|---------|
| **ID** | TC-5-02 |
| **Priority** | Critical |
| **AC** | AC-1 |
| **Type** | Integration (DB) |

**Given:** в `yii2_basic_tests.users` есть запись `login='demo'`, `password=bcrypt('demo')`  
**When:** LoginForm логинит пользователя `demo/demo`  
**Then:** `login()` возвращает `true`, пользователь аутентифицирован

**Verified by:** `LoginFormTest: Login correct + (0.07s)`

---

### TC-5-03: ContactFormTest проходит (Yii2 модуль инициализируется)

| Поле | Значение |
|------|---------|
| **ID** | TC-5-03 |
| **Priority** | High |
| **AC** | AC-1 |
| **Type** | Integration (Yii2 bootstrap) |

**Given:** Yii2 Codeception module подключает `config/test.php` → `config/test_db.php` → `yii2_basic_tests`  
**When:** ContactFormTest запускается  
**Then:** тест проходит — нет ошибки инициализации

**Verified by:** `ContactFormTest: Email is sent on contact + (0.04s)`

---

### TC-5-04: setup-test-db.sh воспроизводимо создаёт БД

| Поле | Значение |
|------|---------|
| **ID** | TC-5-04 |
| **Priority** | High |
| **AC** | AC-1 |
| **Type** | Manual / Smoke |

**Given:** чистая машина, `gold` БД существует, MySQL доступен  
**When:** `bash tests/setup-test-db.sh`  
**Then:** создана `yii2_basic_tests` с полной схемой и фикстурами; unit suite проходит

**Verified by:** выполнено вручную в рамках разработки

---

### TC-5-05: Существующие тесты (FineValidationTest, CreditDebtTest) не регрессировали

| Поле | Значение |
|------|---------|
| **ID** | TC-5-05 |
| **Priority** | Critical |
| **AC** | AC-1 (regression) |
| **Type** | Unit |

**Given:** `yii2_basic_tests` настроена  
**When:** `php vendor/bin/codecept run unit`  
**Then:** FineValidationTest 8/8, CreditDebtTest 8/8 — без изменений

**Verified by:** `OK (24 tests, 47 assertions)` — все предыдущие тесты зелёные

---

## Coverage Summary

| AC | TCs | Статус |
|----|-----|--------|
| AC-1 (no DB errors) | TC-5-01, TC-5-02, TC-5-03, TC-5-04, TC-5-05 | ✅ PASS |
| **Total** | **5 TCs** | **✅ PASS** |
