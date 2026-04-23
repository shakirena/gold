Запусти dev-lead агента для разработки issue.

Аргументы: $ARGUMENTS — номер(а) issue (например: #5, или #5 #6 #7).

## Действие

Прочитай `.claude/agents/dev-lead.md` и выполни полный алгоритм для каждого указанного issue.

Для каждого issue #{N}:
1. Прочитай `.claude/agents/dev-lead.md` — это твои инструкции
2. Прочитай `.claude/agents/quality-gates.md` для G3+G4 чеклистов
3. Прочитай `.claude/agents/dependency-resolver.md` для проверки зависимостей
4. Прочитай `.claude/agents/kanban-board-sync.md` для синхронизации
5. Прочитай `.claude/memory/active-sprint.md` для WIP проверки
6. Следуй алгоритму из dev-lead.md шаг за шагом

Если указано несколько issues:
- Проверь WIP лимит (≤ 5 in-development)
- Независимые issues обрабатывай параллельно
- Зависимые — последовательно

**Важно:** Security review обязателен. `security:*` labels ставит только security-reviewer.
