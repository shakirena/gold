Запусти e2e-tester агента для создания E2E автотестов.

Аргументы: $ARGUMENTS — номер issue (например: #5) или `all` для всех issues с TC docs.

## Действие

Прочитай `.claude/agents/e2e-tester.md` и выполни полный алгоритм.

1. Прочитай `.claude/agents/e2e-tester.md` — это твои инструкции
2. Прочитай `CLAUDE.md` — стек, URL структура
3. Следуй алгоритму из e2e-tester.md шаг за шагом

**Если аргумент `all`:**
```bash
gh issue list --label "kanban:done,type:story" --repo shakirena/gold --json number,title
```
Обработай все issues у которых есть `docs/test-cases/feature-{N}-*.md`.

**Поток E2E:**
- Читает `docs/test-cases/feature-{N}-{name}.md`
- Создаёт Page Objects в `tests/_support/Page/`
- Создаёт Acceptance тесты в `tests/acceptance/`
- Автоматизирует Critical + High TCs обязательно
- Обновляет `docs/test-cases/traceability-tc.md`

**Важно:** E2E тесты НЕ блокируют деплой stories — это отдельный поток.
