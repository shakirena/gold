# Dependency Resolver Protocol

Этот документ читается Team Leads и analyst. Описывает как работать с DAG-зависимостями между issues.

---

## Синтаксис зависимостей в body issue

```
Blocked by: #10, #12
Depends on: #10
Requires: #10, #12, #15
```

Все три синтаксиса эквивалентны. Parser ищет их в body issue.

---

## Алгоритм разрешения

```
1. Прочитать body issue → найти "Blocked by:", "Depends on:", "Requires:"
2. Для каждого blocker #M:
   a. Получить статус: gh issue view {M} --json state,labels --repo shakirena/gold
   b. Если state=closed ИЛИ labels содержит "kanban:done" → NOT blocking
   c. Иначе → BLOCKING
3. Если хотя бы один blocker BLOCKING → issue BLOCKED, skip
4. Если все blockers resolved → можно обрабатывать
```

---

## Состояния

| Состояние blocker | Действие |
|------------------|----------|
| `state: closed` | Не блокирует |
| `kanban:done` label | Не блокирует |
| `state: open`, не `kanban:done` | **BLOCKED** — пропустить issue |
| Circular dependency | Manual intervention — предупредить человека |
| Depth > 10 уровней | **BLOCKED** — предупредить человека |

---

## Circular dependency detection

```
Строим граф: issue → [blockers]
BFS/DFS: если встретили уже посещённый узел → circular dependency
```

При обнаружении circular dependency:
1. НЕ пытаться авторазрешить
2. Написать в comment: "BLOCKED: circular dependency detected between #A → #B → #A. Manual intervention required."
3. Добавить label `blocked`

---

## Команды для проверки

```bash
# Получить info об issue
gh issue view {N} --json number,title,state,labels,body --repo shakirena/gold

# Получить список blockers из body (grep)
gh issue view {N} --json body --repo shakirena/gold | \
  jq -r '.body' | grep -oP '#\d+' 
```

---

## Автодиспатч при разрешении

Когда blocker-issue закрывается, dev-lead/qa-lead при следующем `/sweep` или `/swarm` автоматически подхватят previously-blocked issues.

---

## analyst: добавление зависимостей

При декомпозиции, если Story B зависит от Story A:
- Добавить в body Story B: `Blocked by: #A`
- Добавить в body Story A: `Blocks: #B` (для трассировки)
