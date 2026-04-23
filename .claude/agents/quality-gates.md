# Quality Gates Protocol (G1–G6)

Этот документ читается Team Leads напрямую. Не является агентом.

---

## Gate G1: backlog → analysis
**Enforcer:** analysis-lead  
**Проверки:**
- [ ] Issue содержит описание бизнес-ценности (зачем это нужно)
- [ ] Acceptance Criteria написаны (хотя бы 1 AC)
- [ ] Нет дубликатов (поиск по title в открытых issues)
- [ ] Тип определён (`type:feature`, `type:bug`, `type:tech-debt`, `type:spike`)
- [ ] Приоритет выставлен (`priority:critical/high/medium/low`)
- [ ] Размер оценён (`size:s/m/l` — для feature; `size:xs` — для багов)

**PASS** → добавить `kanban:analysis`, удалить `kanban:backlog`  
**NEEDS WORK** → попросить уточнить AC или бизнес-ценность  
**BLOCKED** → дубликат или вне scope проекта

---

## Gate G2: analysis → ready-for-dev
**Enforcer:** analysis-lead  
**Проверки:**
- [ ] `docs/specs/feature-{N}-{name}.md` создан и содержит FR, NFR, Given/When/Then
- [ ] `docs/arch/feature-{N}-{name}.md` создан (ERD, API, code stubs)
- [ ] Каждая User Story: ровно один G/W/T (SD-1)
- [ ] Каждая Story: INVEST соблюдён (SD-2)
- [ ] Нет `size:xl`, нет "и"/"and" в заголовках stories (SD-3, SD-5)
- [ ] Технические заметки и "вне scope" указаны (SD-4)
- [ ] `.claude/memory/stories/story-{N}.md` создан агентами (📋+🏗️ разделы)

**PASS** → добавить `kanban:ready-for-dev`, удалить `kanban:analysis`  
**NEEDS WORK** → доработать spec или arch doc  
**BLOCKED** → нет arch doc или spec неполный

---

## Gate G3: ready-for-dev → in-development
**Enforcer:** dev-lead  
**Проверки:**
- [ ] `composer install` выполняется без ошибок (или последний run был OK)
- [ ] AC testable: каждый AC имеет ровно один G/W/T
- [ ] Зависимости разрешены (нет открытых blocker-issues)
- [ ] WIP In Development < 5

**PASS** → добавить `kanban:in-development`, удалить `kanban:ready-for-dev`  
**NEEDS WORK** → уточнить AC или разрешить зависимости  
**BLOCKED** → WIP ≥ 5 или неразрешённый blocker

---

## Gate G4: in-development → testing
**Enforcer:** dev-lead  
**Проверки:**
- [ ] `php vendor/bin/codecept run unit` проходит (вывод команды в report)
- [ ] `composer validate` OK
- [ ] Unit-тесты написаны для новых service/model классов
- [ ] Нет orphaned файлов (`.php.bak`, `- Copy.php`, `.bak`)
- [ ] Нет `TODO`/`FIXME` в новом коде
- [ ] `security:passed` label выставлен security-reviewer

**PASS** → добавить `kanban:testing`, удалить `kanban:in-development`  
**NEEDS WORK** → исправить тесты или убрать TODO  
**BLOCKED** → `security:failed` label или тесты падают

---

## Gate G5: testing → ready-to-deploy
**Enforcer:** qa-lead  
**Проверки:**
- [ ] `php vendor/bin/codecept run unit` — coverage ≥ 95% новых файлов (hotfix: ≥ 50%)
- [ ] Все AC верифицированы тестами (mapped в TC docs)
- [ ] `docs/test-cases/feature-{N}-{name}.md` создан
- [ ] `docs/test-cases/traceability-tc.md` обновлён
- [ ] `security:passed` label **присутствует** (qa-lead только проверяет, не ставит)
- [ ] `qa:passed` label выставлен

**PASS** → добавить `kanban:ready-to-deploy`, удалить `kanban:testing`  
**NEEDS WORK** → coverage < порог или AC не покрыты  
**BLOCKED** → `security:passed` отсутствует

---

## Gate G6: ready-to-deploy → done
**Enforcer:** ops-lead  
**Проверки:**
- [ ] `php vendor/bin/codecept run` — все тесты green
- [ ] Docker build успешен (если применимо)
- [ ] Health check endpoint отвечает 200
- [ ] Smoke test основных URL (/ , /site/login, главные CRUD)
- [ ] Нет ERROR в логах первые 60 сек после деплоя (`runtime/logs/`)
- [ ] Rollback-команда задокументирована в comment к issue

**PASS** → добавить `kanban:done`, удалить `kanban:ready-to-deploy`, закрыть issue  
**NEEDS WORK** → предупреждения в логах  
**BLOCKED** → health check падает → НЕМЕДЛЕННЫЙ ROLLBACK

---

## Quality Gate Report Template

Каждый Team Lead оставляет comment в issue после gate с реальным выводом команд:

```
## Quality Gate Report: G{N} — {PASS ✅ | NEEDS WORK ⚠️ | BLOCKED 🚫}

**Gate:** G{N} ({from} → {to})
**Date:** {datetime}
**Agent:** {agent-name}

### Checks

| # | Check | Result |
|---|-------|--------|
| 1 | {check description} | ✅ / ❌ |
| 2 | {check description} | ✅ / ❌ |

### Command Output

```
{actual command output — не утверждения}
```

### Decision

{PASS: переходим в {next-column}} 
{NEEDS WORK: что нужно исправить}
{BLOCKED: причина блокировки}
```

**ВАЖНО:** Reports содержат реальный вывод команд, а не утверждения типа "тесты прошли".
