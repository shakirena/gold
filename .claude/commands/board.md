Покажи текущее состояние Kanban-доски.

Аргументы: $ARGUMENTS — опционально фильтр (например: `blocked`, `wip`, `#5`).

## Действие

Собери и визуализируй состояние Kanban-доски из GitHub Issues.

### 1. Получить issues по всем колонкам

```bash
gh issue list --repo shakirena/gold --label "kanban:backlog" --json number,title,labels,assignees --limit 20
gh issue list --repo shakirena/gold --label "kanban:analysis" --json number,title,labels,assignees --limit 20
gh issue list --repo shakirena/gold --label "kanban:ready-for-dev" --json number,title,labels,assignees --limit 20
gh issue list --repo shakirena/gold --label "kanban:in-development" --json number,title,labels,assignees --limit 20
gh issue list --repo shakirena/gold --label "kanban:testing" --json number,title,labels,assignees --limit 20
gh issue list --repo shakirena/gold --label "kanban:ready-to-deploy" --json number,title,labels,assignees --limit 20
```

### 2. Сформировать и вывести доску

Выведи в формате:

```
═══════════════════════════════════════════════════════════
                    KANBAN BOARD — shakirena/gold
═══════════════════════════════════════════════════════════

📋 BACKLOG (N)          🔍 ANALYSIS (N)         ✅ READY-FOR-DEV (N)
─────────────────       ─────────────────       ─────────────────
#N title [P][S]         #N title [P][S]         #N title [P][S]
...                     ...                     ...

🔧 IN-DEV (N/5)         🧪 TESTING (N/5)        🚀 READY-TO-DEPLOY (N)
─────────────────       ─────────────────       ─────────────────
#N title [P][S]         #N title [P][S]         #N title [P][S]
...                     ...                     ...
```

Где: [P] = приоритет (C/H/M/L), [S] = size (xs/s/m/l)

### 3. Показать блокировки

Найти issues с `Blocked by:` в body у которых blocker ещё открыт:
```bash
gh issue list --repo shakirena/gold --json number,body,labels | \
  jq '.[] | select(.body | contains("Blocked by:")) | {number, body}'
```

Вывести секцию:
```
🚫 БЛОКИРОВКИ
#N заблокирован #M (open)
```

### 4. WIP статус

```
⚠️ WIP: In-Development: N/5  |  Testing: N/5
```

Если N ≥ 5 — показать предупреждение.

### 5. Метрики (если нет фильтра)

- Всего открытых issues
- В работе (in-development + testing)
- Готово к деплою
- Заблокировано
