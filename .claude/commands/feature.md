Создай GitHub Issue для новой фичи в репозитории shakirena/gold.

Аргументы: $ARGUMENTS — описание фичи на русском или английском.

## Шаги

1. Проанализируй описание фичи из аргументов.

2. Определи:
   - **Тип:** `type:feature` (новая функциональность) или `type:bug` (исправление)
   - **Приоритет:** `priority:critical/high/medium/low` — на основе бизнес-ценности
   - **Размер:** `size:s/m/l` — оценочно по сложности
   - **Компонент:** `component:backend`, `component:frontend`, `component:infra`, или несколько

3. Сформируй body issue:

```
## Описание
{краткое описание что нужно сделать}

## Бизнес-ценность
{зачем это нужно, какую проблему решает}

## Acceptance Criteria
- [ ] AC-1: {конкретное проверяемое условие}
- [ ] AC-2: {конкретное проверяемое условие}

## Технические заметки
{стек, особенности реализации в Yii2 если очевидны}
```

4. Создай issue:
```bash
gh issue create \
  --title "{краткий title}" \
  --body "{body}" \
  --label "type:feature,kanban:backlog,priority:{P},size:{S},component:{C}" \
  --repo shakirena/gold
```

5. Выведи ссылку на созданный issue.

**Следующий шаг:** `/analyze #N` для декомпозиции фичи на User Stories.
