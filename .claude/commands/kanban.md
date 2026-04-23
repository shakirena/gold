Полный pipeline: от описания фичи до деплоя (Feature → Analysis → Develop → Test → Deploy).

Аргументы: $ARGUMENTS — описание фичи на русском или английском.

## Действие

Запускает полный Kanban pipeline через все 6 Quality Gates.

### Шаг 1: Создать Feature Issue

Аналогично `/feature`:
1. Проанализируй описание из аргументов
2. Определи тип, приоритет, размер, компонент
3. Создай issue в `shakirena/gold` с labels и AC
4. Запомни номер созданного issue #{N}

### Шаг 2: Analysis (G1 → G2)

Прочитай `.claude/agents/analysis-lead.md` и выполни полный алгоритм для #{N}:
- G1: backlog → analysis
- analyst: создать spec в `docs/specs/feature-{N}-{name}.md`
- architect: создать arch doc в `docs/arch/feature-{N}-{name}.md`
- G2: analysis → ready-for-dev
- doc-sync: обновить traceability.md

Дождись завершения. Если G1 или G2 BLOCKED — остановиться, сообщить причину.

### Шаг 3: Development (G3 → G4)

Прочитай `.claude/agents/dev-lead.md` и выполни полный алгоритм:
- Dependency check
- WIP check (< 5)
- G3: ready-for-dev → in-development
- developer: реализация в worktree (branch: `feature/{N}-{name}`)
- G4: code checks
- security-reviewer: OWASP review → `security:passed` / `security:failed`
- kanban: in-development → testing

Дождись завершения. Если любой Gate BLOCKED — остановиться.

### Шаг 4: Testing (G5)

Прочитай `.claude/agents/qa-lead.md` и выполни алгоритм:
- Проверить `security:passed`
- tester: unit tests (coverage ≥ 95%)
- test-case-writer: TC docs
- G5: testing → ready-to-deploy

Дождись завершения. Если G5 BLOCKED — остановиться.

### Шаг 5: Deploy (G6)

Прочитай `.claude/agents/ops-lead.md` и выполни деплой:
- G6 Pre-flight: qa:passed + security:passed + build OK
- devops: deploy → health check → smoke test
- G6 Post-deploy: health 200, logs clean
- kanban: ready-to-deploy → done, issue closed

### Итоговый отчёт

```
═══ KANBAN PIPELINE COMPLETE ═══
Feature: #{N} — {title}

✅ G1: PASS
✅ G2: PASS  
✅ G3: PASS
✅ G4: PASS (security:passed)
✅ G5: PASS (coverage: X%)
✅ G6: PASS (deployed:staging)

Issue #{N} закрыт. 🎉
```

**При блокировке на любом Gate:**
```
🚫 PIPELINE BLOCKED на Gate GN
Причина: {причина}
Требуется: {что нужно сделать}
Возобновить с: /develop #{N}  (или /test #{N}, /deploy staging)
```
