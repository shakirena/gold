Полная инициализация агентской системы на проекте. Запускать один раз.

Аргументы: $ARGUMENTS — опционально `check` для только проверки состояния без изменений.

## Действие

### Режим check

Если аргумент `check` — только проверить состояние, не создавать ничего.

### Шаг 1: Проверить окружение

```bash
# gh CLI доступен?
gh --version

# Авторизован?
gh auth status

# Репозиторий доступен?
gh repo view shakirena/gold --json name,defaultBranchRef

# PHP + Composer?
php --version
composer --version

# Docker?
docker --version
```

Если `gh` недоступен или не авторизован — СТОП, вывести инструкции по настройке.

### Шаг 2: Проверить структуру .claude/

Убедиться что все файлы присутствуют:
- `.claude/AGENTS_FRAMEWORK.md`
- `.claude/agents/analysis-lead.md`
- `.claude/agents/dev-lead.md`
- `.claude/agents/qa-lead.md`
- `.claude/agents/ops-lead.md`
- `.claude/agents/analyst.md`
- `.claude/agents/architect.md`
- `.claude/agents/developer.md`
- `.claude/agents/security-reviewer.md`
- `.claude/agents/tester.md`
- `.claude/agents/test-case-writer.md`
- `.claude/agents/e2e-tester.md`
- `.claude/agents/devops.md`
- `.claude/agents/quality-gates.md`
- `.claude/agents/kanban-board-sync.md`
- `.claude/agents/dependency-resolver.md`

### Шаг 3: Создать структуру директорий

```bash
mkdir -p docs/specs
mkdir -p docs/arch
mkdir -p docs/test-cases
mkdir -p .claude/memory/stories
mkdir -p .claude/handoffs
```

### Шаг 4: Создать memory файлы

Если не существуют — создать заготовки:

**`.claude/memory/project-summary.md`** — краткое описание проекта (из CLAUDE.md):
```markdown
# Gold — Project Summary
Система управления кредитами и займами. Yii2 Basic, PHP 7.1+, MySQL 5.7.
Repo: shakirena/gold | Branch: master | Local: D:\OSPanel\domains\gold
Stack: Yii2, Bootstrap 3, jQuery, Kartik, Codeception 2.3
```

**`.claude/memory/active-sprint.md`** — текущий WIP:
```markdown
# Active Sprint

## In Development (0/5)
_пусто_

## Testing (0/5)
_пусто_

Last updated: {дата}
```

**`.claude/memory/decisions.md`** — архитектурные решения:
```markdown
# Architectural Decisions

_Решения появятся здесь после первых разработок._
```

**`docs/test-cases/traceability-tc.md`** — матрица трассируемости:
```markdown
# Test Cases Traceability Matrix

| Feature | Story | TC ID | TC Name | Priority | E2E Automated |
|---------|-------|-------|---------|----------|---------------|
```

### Шаг 5: Проверить GitHub Labels

```bash
gh label list --repo shakirena/gold --json name | jq '.[].name' | grep "kanban:"
```

Если меток нет — предложить `/setup-board`.

### Шаг 6: Итоговый отчёт

```
═══ INIT COMPLETE ═══

✅ gh CLI: OK (v{version})
✅ Авторизация: OK ({user})
✅ Репозиторий: shakirena/gold (branch: master)
✅ PHP: {version}
✅ Структура .claude/: OK (все агенты на месте)
✅ Директории: созданы
✅ Memory файлы: инициализированы
{✅/⚠️} GitHub Labels: N/35 созданы

Система готова к работе!
Начни с: /feature <описание фичи>
```
