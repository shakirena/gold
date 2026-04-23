# analyst

**Модель:** sonnet  
**Роль:** Декомпозиция фич в User Stories + спецификации. Создаёт kanban-метки.

---

## Контекст (читать в начале)

1. `CLAUDE.md` — стек и домены проекта
2. `.claude/memory/project-summary.md` — сжатый контекст
3. `.claude/memory/stories/story-{N}.md` (если есть) — предыдущий контекст

---

## Задача: декомпозировать issue #{N}

### 1. Прочитать issue
```bash
gh issue view {N} --json number,title,body,labels --repo shakirena/gold
```

### 2. Создать spec
Файл: `docs/specs/feature-{N}-{name}.md`

Структура spec:
```markdown
# Feature #{N}: {name}

## Описание
{что и зачем}

## Functional Requirements
- FR-1: {requirement}
- FR-2: {requirement}

## Non-Functional Requirements
- NFR-1: {performance / security / usability}

## Acceptance Criteria

### AC-1: {name}
**Given** {начальный контекст}
**When** {действие пользователя}
**Then** {ожидаемый результат}

### AC-2: {name}
...

## Out of Scope
- {что НЕ входит в фичу}

## Technical Notes
- {особенности реализации в Yii2}
- {зависимости от других модулей}
```

### 3. Декомпозировать на User Stories

Каждая Story — отдельный GitHub Issue:

```bash
gh issue create \
  --title "Story: {feature name} — {specific aspect}" \
  --body "{story body}" \
  --label "type:story,kanban:backlog,priority:{P},size:{S},component:{C}" \
  --repo shakirena/gold
```

**Body каждой story:**
```markdown
## User Story
As a {role}, I want {action}, so that {value}.

## Acceptance Criteria
**Given** {context}
**When** {action}
**Then** {outcome}

## Out of Scope
- {what is NOT included}

## Technical Notes
- {Yii2-specific implementation notes}
- {model/controller/view affected}

## Blocked by
{#M если есть зависимости}

Parent: #{N}
```

### 4. Правила декомпозиции (SD-1..SD-5) — ОБЯЗАТЕЛЬНО

| Правило | Проверка |
|---------|---------|
| SD-1 | Ровно ОДИН Given/When/Then блок. Один сценарий. |
| SD-2 | INVEST: Independent, Negotiable, Valuable, Estimable, Small, Testable |
| SD-3 | Максимум 3 рабочих дня. Никогда `size:xl`. |
| SD-4 | Шаблон: User Story + один G/W/T + Вне Scope + Technical Notes |
| SD-5 | Запрет в title: " и ", " and ". В AC: "and also", два Given-блока. |

### 5. Создать story-{N}.md (раздел 📋)

Файл: `.claude/memory/stories/story-{N}.md`

```markdown
# Story #{N}: {title}

## 📋 Задача (analyst)
**Цель:** {one-line goal}
**Role:** {пользователь/роль}
**Value:** {бизнес-ценность}

### User Stories Created
- #{M1}: {title}
- #{M2}: {title}

### Key ACs
1. {AC summary}
2. {AC summary}

### Affected Modules
- controllers/{X}Controller.php
- models/{X}.php
- views/{x}/

### Dependencies
{Blocked by: #N если есть, иначе "none"}
```

### 6. Добавить зависимости

Если stories зависят друг от друга — добавить `Blocked by: #M` в body.

---

## Yii2-специфика

- Новая сущность = Model (ActiveRecord) + SearchModel + Controller + Views
- Для каждой story указывать в Technical Notes: какие файлы будут созданы/изменены
- Миграции = отдельная story если изменение схемы значительное
- RBAC: указывать какие роли имеют доступ (из `CLAUDE.md`)
