Запусти все Team Leads параллельно по всей доске для максимальной скорости.

Аргументы: $ARGUMENTS — опционально `develop`, `test`, `analyze`, `dry-run` для ограничения области.

## Действие

Swarm запускает все применимые Team Leads одновременно. Используй когда нужна максимальная скорость и доска содержит задачи в нескольких колонках.

**Важно:** В отличие от `/sweep`, swarm запускает Team Leads ПАРАЛЛЕЛЬНО. Возможны race conditions на labels — используй только когда issues независимы.

### Режим dry-run

Если аргумент `dry-run`:
1. Собери список issues из каждой колонки
2. Покажи план: какие агенты будут запущены для каких issues
3. Завершить без реальных действий

### Шаг 1: Собрать состояние доски

```bash
gh issue list --repo shakirena/gold --label "kanban:ready-to-deploy" --json number,title,labels --limit 20
gh issue list --repo shakirena/gold --label "kanban:testing" --json number,title,labels --limit 20
gh issue list --repo shakirena/gold --label "kanban:ready-for-dev" --json number,title,labels --limit 20
gh issue list --repo shakirena/gold --label "kanban:backlog" --json number,title,labels --limit 20
```

### Шаг 2: Определить режим

- Без аргумента → запустить все применимые Team Leads
- `develop` → только dev-lead для `kanban:ready-for-dev`
- `test` → только qa-lead для `kanban:testing`
- `analyze` → только analysis-lead для `kanban:backlog`

### Шаг 3: Параллельный запуск Team Leads

Запустить ОДНОВРЕМЕННО (используя параллельные вызовы агентов):

**ops-lead** для каждого `kanban:ready-to-deploy` issue:
- Прочитай `.claude/agents/ops-lead.md`
- Следуй алгоритму G6 → deploy

**qa-lead** для каждого `kanban:testing` issue (WIP < 5):
- Прочитай `.claude/agents/qa-lead.md`
- Следуй алгоритму G5 → тестирование

**dev-lead** для каждого `kanban:ready-for-dev` issue (WIP < 5):
- Прочитай `.claude/agents/dev-lead.md`
- Следуй алгоритму G3 → разработка

**analysis-lead** для каждого `kanban:backlog` issue с `priority:critical` или `priority:high`:
- Прочитай `.claude/agents/analysis-lead.md`
- Следуй алгоритму G1 → анализ

### Шаг 4: Сводный отчёт

После завершения всех агентов:
```
═══ SWARM COMPLETE ═══
ops-lead:      N задач обработано
qa-lead:       N задач обработано
dev-lead:      N задач обработано
analysis-lead: N задач обработано
Ошибки/BLOCKED: N (перечислить)
```
