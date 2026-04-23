Запусти analysis-lead агента для анализа и декомпозиции issue.

Аргументы: $ARGUMENTS — номер issue (например: #5) или несколько (#5 #6 #7).

## Действие

Прочитай `.claude/agents/analysis-lead.md` и выполни полный алгоритм analysis-lead для каждого указанного issue.

Для каждого issue #{N}:
1. Прочитай `.claude/agents/analysis-lead.md` — это твои инструкции
2. Прочитай `.claude/agents/quality-gates.md` для G1+G2 чеклистов
3. Прочитай `.claude/agents/kanban-board-sync.md` для синхронизации
4. Следуй алгоритму из analysis-lead.md шаг за шагом

Если указано несколько issues — обработай их параллельно (независимые issues) или последовательно (если есть зависимости).
