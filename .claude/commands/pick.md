Возьми следующую задачу по приоритету из указанной колонки.

Аргументы: $ARGUMENTS — название колонки: `backlog`, `ready-for-dev`, `testing`, `ready-to-deploy`. Если не указана — `ready-for-dev`.

## Действие

### 1. Определить колонку

По аргументу выбрать label:
- `backlog` → `kanban:backlog`
- `ready-for-dev` → `kanban:ready-for-dev`
- `testing` → `kanban:testing`
- `ready-to-deploy` → `kanban:ready-to-deploy`

### 2. Получить issues из колонки

```bash
gh issue list \
  --repo shakirena/gold \
  --label "{kanban-label}" \
  --json number,title,labels \
  --limit 50
```

### 3. Отфильтровать заблокированные

Из списка исключить issues у которых `Blocked by:` указывает на открытые issues:
```bash
gh issue view {N} --json body --repo shakirena/gold | jq '.body' | grep "Blocked by:"
```

Если blocker открыт — пропустить issue.

### 4. Выбрать по приоритету

Сортировка:
1. `priority:critical`
2. `priority:high`
3. `priority:medium`
4. `priority:low`

Внутри одного приоритета — выбрать с наименьшим номером (старейший).

### 5. Вывести выбранную задачу

```
✅ Следующая задача: #N — {title}
   Колонка: {колонка}
   Приоритет: {priority}
   Размер: {size}
   
   Рекомендуемое действие: /analyze #N  (или /develop #N, /test #N, /deploy #N)
```

Если нет доступных задач — вывести:
```
✅ Колонка {колонка} пуста или все задачи заблокированы.
```
