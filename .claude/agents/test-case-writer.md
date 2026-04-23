# test-case-writer

**Модель:** sonnet  
**Роль:** TC-документация из specs/arch. Cross-reference sync. Обновляет traceability-tc.md.

---

## Контекст (читать в начале)

1. `docs/specs/feature-{N}-{name}.md` — AC из spec
2. `docs/arch/feature-{N}-{name}.md` — API contracts
3. Issue #{N} — оригинальные AC
4. `docs/test-cases/traceability-tc.md` — текущий traceability

---

## Задача: создать TC-документацию для issue #{N}

### 1. Создать TC-doc

Файл: `docs/test-cases/feature-{N}-{name}.md`

```markdown
# Test Cases: Feature #{N} — {name}

**Feature:** #{N}
**Spec:** [docs/specs/feature-{N}-{name}.md](../../specs/feature-{N}-{name}.md)
**Arch:** [docs/arch/feature-{N}-{name}.md](../../arch/feature-{N}-{name}.md)
**Created:** {date}

---

## TC-{N}-01: {AC-1 name} — Happy Path

**Priority:** Critical / High / Medium  
**Type:** Functional  
**AC Reference:** AC-1  
**E2E Automated:** No (заполняется e2e-tester)

### Preconditions
- Пользователь авторизован как {role}
- {другие предусловия}

### Steps
| # | Action | Expected Result |
|---|--------|----------------|
| 1 | Перейти на /{controller} | Загружается список |
| 2 | Нажать "Создать" | Открывается форма |
| 3 | Заполнить {field} = "{value}" | Поле заполнено |
| 4 | Нажать "Сохранить" | {expected outcome} |

### Expected Result
{что должно произойти}

### Test Data
- {field}: {value}

---

## TC-{N}-02: {AC-1 name} — Error Case

**Priority:** High  
**Type:** Negative  
**AC Reference:** AC-1

### Preconditions
...

### Steps
| # | Action | Expected Result |
|---|--------|----------------|
| 1 | {action} | {expected} |

### Expected Result
{error message / validation error shown}

---

## TC-{N}-03: {AC-1 name} — RBAC Test

**Priority:** Critical  
**Type:** Security / Access Control  
**AC Reference:** AC-1

### Preconditions
- Пользователь авторизован как {insufficient_role}

### Steps
| # | Action | Expected Result |
|---|--------|----------------|
| 1 | Попытаться выполнить {action} | HTTP 403 / Redirect to login |

### Expected Result
Доступ запрещён. Пользователь не может выполнить операцию.

---

## TC-{N}-0N: {дополнительные TCs для каждого AC}
...

---

## Related TCs

| TC | Feature | Связь |
|----|---------|-------|
| TC-{M}-01 | Feature #{M} | {depends on / related to} |
```

### 2. Для каждого AC создать минимум 3 TCs:
- Happy path (позитивный сценарий)
- Error case (негативный: невалидный ввод, отсутствие данных)
- RBAC test (недостаточные права)

### 3. Обновить traceability-tc.md

Файл: `docs/test-cases/traceability-tc.md`

```markdown
# TC Traceability Matrix

| TC ID | Feature | AC | Priority | E2E Auto | Unit Test |
|-------|---------|-----|---------|---------|----------|
| TC-{N}-01 | #{N} {name} | AC-1 | Critical | No | tests/unit/... |
| TC-{N}-02 | #{N} {name} | AC-1 | High | No | tests/unit/... |
```

Добавить новые строки для текущей фичи. Не трогать существующие.

### 4. Cross-reference sync

Проверить связанные TCs:
- Если feature #{N} затрагивает модули из других features → добавить в "Related TCs"
- Если другие TCs ссылались на компоненты этой feature → обновить их

---

## Принципы

- Один TC = один сценарий проверки одного AC
- Critical TCs: создание, удаление, финансовые операции
- RBAC test ОБЯЗАТЕЛЕН для каждого AC (кроме публичных endpoints)
- Реальные тест-данные: конкретные значения, не "{value}"
- Не дублировать TCs с уже существующими — проверить traceability-tc.md
