Автопилот: обработай всю доску right-to-left (deploy → test → dev → analysis).

Аргументы: $ARGUMENTS — опционально `dry-run` (только показать что будет делать), или название конкретной колонки (`ready-to-deploy`, `testing`, `in-development`, `ready-for-dev`).

## Действие

Sweep обходит колонки справа налево, чтобы гарантировать порядок и минимизировать race conditions.

### Режим dry-run

Если аргумент `dry-run`:
1. Собери список issues из каждой колонки
2. Покажи что было бы запущено — без реальных действий
3. Завершить

### Шаг 1: Собрать состояние доски

```bash
gh issue list --repo shakirena/gold --label "kanban:ready-to-deploy" --json number,title,labels --limit 20
gh issue list --repo shakirena/gold --label "kanban:testing" --json number,title,labels --limit 20
gh issue list --repo shakirena/gold --label "kanban:in-development" --json number,title,labels --limit 20
gh issue list --repo shakirena/gold --label "kanban:ready-for-dev" --json number,title,labels --limit 20
```

### Шаг 2: Проверить фильтр колонки

Если указана конкретная колонка — обработать только её.
Если не указана — обработать все колонки по порядку ниже.

### Шаг 3: Right-to-left обработка

**Порядок СТРОГО:**

#### 3.1 ready-to-deploy → /deploy staging
Для каждого незаблокированного issue:
- Прочитай `.claude/agents/ops-lead.md`
- Выполни G6 Pre-flight
- Задеплой на staging

#### 3.2 testing → /test
Для каждого незаблокированного issue (WIP < 5):
- Прочитай `.claude/agents/qa-lead.md`
- Проверь `security:passed`
- Запусти qa-lead

#### 3.3 in-development (только если есть зависшие) → проверить статус
Проверить issues без активного PR — переспросить dev-lead статус.

#### 3.4 ready-for-dev → /develop
Для незаблокированных issues (WIP < 5):
- Прочитай `.claude/agents/dev-lead.md`
- Запусти dev-lead

#### 3.5 backlog (опционально) → /analyze
Только если указан `backlog` явно:
- Прочитай `.claude/agents/analysis-lead.md`
- Запусти analysis-lead

### Шаг 4: Итоговый отчёт

После обработки всех колонок вывести:
```
═══ SWEEP COMPLETE ═══
Задеплоено:    N issues
Протестировано: N issues
Разработано:   N issues
Заблокировано: N issues (перечислить)
```

**Важно:** Каждый этап ждёт завершения предыдущего — не запускать параллельно между колонками.
