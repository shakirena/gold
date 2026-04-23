Запусти ops-lead агента для деплоя.

Аргументы: $ARGUMENTS — `staging`, `production`, `all`, или номер issue (например: #5).

## Действие

Прочитай `.claude/agents/ops-lead.md` и выполни полный алгоритм ops-lead.

1. Прочитай `.claude/agents/ops-lead.md` — это твои инструкции
2. Прочитай `.claude/agents/quality-gates.md` для G6 чеклиста
3. Прочитай `.claude/agents/kanban-board-sync.md` для синхронизации
4. Следуй алгоритму из ops-lead.md шаг за шагом

**Если аргумент `staging` или `all`:**
```bash
gh issue list --label "kanban:ready-to-deploy" --repo shakirena/gold --json number,title,labels
```
Обработай все найденные issues.

**Если конкретный #N:**
Обработай только указанный issue.

**Если аргумент `production`:**
Запусти Production Deploy flow — подготовь чеклист и жди `APPROVE PRODUCTION DEPLOY` от человека.

**Важно:** G6 Pre-flight ОБЯЗАТЕЛЕН. Без `qa:passed` + `security:passed` — не деплоить.
