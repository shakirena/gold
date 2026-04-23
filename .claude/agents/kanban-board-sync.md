# Kanban Board Sync Protocol

Этот документ читается Team Leads напрямую. Описывает как синхронизировать GitHub Labels + Project Board.

---

## Правило: Status-first

**СНАЧАЛА обновить статус → ПОТОМ начинать работу.**

При каждой смене колонки:
1. Убрать старый `kanban:*` label
2. Добавить новый `kanban:*` label
3. Обновить статус в GitHub Project Board

---

## Label операции (через gh CLI)

```bash
# Добавить label
gh issue edit {N} --add-label "kanban:in-development" --repo shakirena/gold

# Убрать label
gh issue edit {N} --remove-label "kanban:ready-for-dev" --repo shakirena/gold

# Добавить + убрать одновременно
gh issue edit {N} \
  --add-label "kanban:in-development" \
  --remove-label "kanban:ready-for-dev" \
  --repo shakirena/gold
```

---

## Project Board Sync

Для синхронизации с GitHub Project Board используем `gh project` CLI:

```bash
# Найти project ID
gh project list --owner shakirena

# Найти item ID для issue
gh project item-list {PROJECT_NUMBER} --owner shakirena --format json | \
  jq '.items[] | select(.content.number == {ISSUE_N}) | .id'

# Обновить статус
gh project item-edit \
  --project-id {PROJECT_ID} \
  --id {ITEM_ID} \
  --field-id {STATUS_FIELD_ID} \
  --single-select-option-id {OPTION_ID}
```

### Статусы Project Board → Label mapping

| Project Status | kanban: label |
|---------------|---------------|
| Backlog | `kanban:backlog` |
| Analysis | `kanban:analysis` |
| Ready for Dev | `kanban:ready-for-dev` |
| In Development | `kanban:in-development` |
| Testing | `kanban:testing` |
| Ready to Deploy | `kanban:ready-to-deploy` |
| Done | `kanban:done` |

---

## Если gh project недоступен

При отсутствии project или недостатке прав — обновлять только labels через `gh issue edit`.  
Логировать в comment: "Board sync skipped — project not configured. Labels updated."

---

## Порядок операций для каждого перехода

### backlog → analysis (G1 PASS)
```bash
gh issue edit {N} --add-label "kanban:analysis" --remove-label "kanban:backlog" --repo shakirena/gold
# + project board sync
```

### analysis → ready-for-dev (G2 PASS)
```bash
gh issue edit {N} --add-label "kanban:ready-for-dev" --remove-label "kanban:analysis" --repo shakirena/gold
```

### ready-for-dev → in-development (G3 PASS)
```bash
gh issue edit {N} --add-label "kanban:in-development" --remove-label "kanban:ready-for-dev" --repo shakirena/gold
```

### in-development → testing (G4 PASS + security:passed)
```bash
gh issue edit {N} --add-label "kanban:testing" --remove-label "kanban:in-development" --repo shakirena/gold
```

### testing → ready-to-deploy (G5 PASS)
```bash
gh issue edit {N} --add-label "kanban:ready-to-deploy,qa:passed" --remove-label "kanban:testing" --repo shakirena/gold
```

### ready-to-deploy → done (G6 PASS)
```bash
gh issue edit {N} --add-label "kanban:done,deployed:staging" --remove-label "kanban:ready-to-deploy" --repo shakirena/gold
gh issue close {N} --repo shakirena/gold
```
