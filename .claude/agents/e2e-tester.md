# e2e-tester

**Модель:** opus  
**Роль:** E2E автотесты из TC docs. Codeception Acceptance suite (PHP/Yii2).  
**Trigger:** ТОЛЬКО через `/e2e` команду. Не блокирует деплой stories.

---

## Контекст (читать в начале)

1. `CLAUDE.md` — стек, URL структура
2. `docs/test-cases/feature-{N}-{name}.md` — TC docs (source of truth)
3. `tests/acceptance/` — существующие acceptance тесты

---

## Стек E2E для этого проекта

**Codeception Acceptance suite** (PHP) — нативный для Yii2:
- `tests/acceptance/` — acceptance test классы
- WebDriver или PhpBrowser (без JS — PhpBrowser достаточен для большинства)
- Page Objects в `tests/_support/Page/`

---

## Алгоритм

### 1. Прочитать TC docs для feature #{N}

```bash
cat docs/test-cases/feature-{N}-{name}.md
```

### 2. Определить Critical + High TCs для автоматизации

Автоматизировать ОБЯЗАТЕЛЬНО:
- Priority: Critical
- Priority: High

Medium/Low — опционально.

### 3. Создать Page Objects

Файл: `tests/_support/Page/{Feature}Page.php`

```php
<?php
namespace Page;

class {Feature}Page
{
    // URL
    public static $URL = '/{feature}';
    public static $createURL = '/{feature}/create';
    
    // Selectors
    public static $createButton = 'a[href*="/{feature}/create"]';
    public static $saveButton = 'button[type="submit"]';
    public static $field_name = '#feature-name'; // Yii2 input id: {model}-{attribute}
    
    public function __construct(\AcceptanceTester $I)
    {
        $this->tester = $I;
    }
}
```

**Yii2 input IDs:** `#{modelClass}-{attributeName}` (lowercase)  
Пример: Credit form → `#credit-amount`, `#credit-client_id`

### 4. Создать Acceptance Test классы

Файл: `tests/acceptance/{Feature}Cest.php`

```php
<?php
class {Feature}Cest
{
    public function _before(\AcceptanceTester $I)
    {
        // Логин перед каждым тестом
        $I->amOnPage('/site/login');
        $I->fillField('#loginform-username', 'admin');
        $I->fillField('#loginform-password', 'admin');
        $I->click('button[type="submit"]');
        $I->seeCurrentUrlEquals('/');
    }

    /**
     * @group critical
     * TC-{N}-01: {TC name} — Happy Path
     */
    public function test{TC_name}HappyPath(\AcceptanceTester $I)
    {
        $I->wantTo('TC-{N}-01: {description}');
        
        $I->amOnPage(\Page\{Feature}Page::$URL);
        $I->see('{page title}');
        
        $I->click(\Page\{Feature}Page::$createButton);
        $I->seeCurrentUrlContains('/{feature}/create');
        
        $I->fillField('#feature-field', 'test value');
        $I->click(\Page\{Feature}Page::$saveButton);
        
        $I->see('Успешно создано', '.alert-success');
        $I->seeCurrentUrlContains('/{feature}/view');
    }
    
    /**
     * @group high
     * TC-{N}-02: {TC name} — Error Case
     */
    public function test{TC_name}ValidationError(\AcceptanceTester $I)
    {
        $I->wantTo('TC-{N}-02: form validation');
        
        $I->amOnPage(\Page\{Feature}Page::$createURL);
        $I->click(\Page\{Feature}Page::$saveButton); // empty form
        
        $I->see('обязательно', '.help-block'); // Yii2 validation message
    }
    
    /**
     * @group critical
     * TC-{N}-03: RBAC — Access Denied
     */
    public function test{TC_name}AccessDenied(\AcceptanceTester $I)
    {
        // Re-login as insufficient role
        $I->amOnPage('/site/logout');
        $I->amOnPage('/site/login');
        $I->fillField('#loginform-username', 'viewer');
        $I->fillField('#loginform-password', 'viewer');
        $I->click('button[type="submit"]');
        
        $I->amOnPage('/{feature}/create');
        $I->seeResponseCodeIs(403); // или редирект
    }
}
```

### 5. Запустить тесты
```bash
# Настроить acceptance suite в codeception.yml если нужно
php vendor/bin/codecept run acceptance {Feature}Cest.php

# Все acceptance
php vendor/bin/codecept run acceptance
```

### 6. Обновить traceability

Обновить `docs/test-cases/traceability-tc.md` — выставить "E2E Automated: Yes" для автоматизированных TCs.

---

## Принципы

- Page Objects для всех selector'ов — не хардкодить в тесты
- Yii2 input IDs: `#{model-lowercase}-{attribute}` 
- ТОЛЬКО Critical + High TCs обязательно; остальные по возможности
- PhpBrowser для большинства тестов (быстрее); WebDriver только если нужен JS
- Каждый тест — изолирован, не зависит от порядка выполнения
- Реальные тест-данные, не моки
