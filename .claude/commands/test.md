Запусти qa-lead агента для тестирования issue.

Аргументы: $ARGUMENTS — номер issue (например: #5) или "all" для всех issues в колонке testing.

## Действие

Прочитай `.claude/agents/qa-lead.md` и выполни полный алгоритм.

1. Прочитай `.claude/agents/qa-lead.md` — это твои инструкции
2. Прочитай `.claude/agents/quality-gates.md` для G5 чеклиста
3. Прочитай `.claude/agents/dependency-resolver.md`
4. Прочитай `.claude/agents/kanban-board-sync.md`
5. Следуй алгоритму из qa-lead.md шаг за шагом

Если аргумент "all":
```bash
gh issue list --label "kanban:testing" --repo shakirena/gold --json number,title
```
Обработай все найденные issues.

**Важно:** `security:passed` label ОБЯЗАТЕЛЕН перед тестированием. Без него — BLOCKED.
