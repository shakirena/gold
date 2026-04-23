Создай 35 GitHub-меток и настрой Project Board для shakirena/gold. Запускать один раз.

Аргументы: $ARGUMENTS — опционально `labels-only`, `board-only`, `dry-run`.

## Действие

### dry-run: показать что будет создано, не создавать

### Шаг 1: Создать GitHub Labels

Создать 35 меток через `gh label create`. Если метка уже существует — пропустить (`--force` для обновления цвета).

#### Kanban-колонки
```bash
gh label create "kanban:backlog"          --color "0E8A16" --description "Backlog" --repo shakirena/gold
gh label create "kanban:analysis"         --color "0075CA" --description "In Analysis" --repo shakirena/gold
gh label create "kanban:ready-for-dev"    --color "6F42C1" --description "Ready for Development" --repo shakirena/gold
gh label create "kanban:in-development"   --color "FBCA04" --description "In Development" --repo shakirena/gold
gh label create "kanban:testing"          --color "E4B9C0" --description "In Testing" --repo shakirena/gold
gh label create "kanban:ready-to-deploy"  --color "F9D0C4" --description "Ready to Deploy" --repo shakirena/gold
gh label create "kanban:done"             --color "006B75" --description "Done" --repo shakirena/gold
```

#### Типы
```bash
gh label create "type:feature"    --color "A2EEEF" --description "New feature" --repo shakirena/gold
gh label create "type:epic"       --color "3E4B9E" --description "Epic" --repo shakirena/gold
gh label create "type:story"      --color "7057FF" --description "User Story" --repo shakirena/gold
gh label create "type:bug"        --color "D73A4A" --description "Bug" --repo shakirena/gold
gh label create "type:tech-debt"  --color "E4E669" --description "Technical debt" --repo shakirena/gold
gh label create "type:hotfix"     --color "B60205" --description "Hotfix" --repo shakirena/gold
gh label create "type:spike"      --color "BFD4F2" --description "Research spike" --repo shakirena/gold
```

#### Приоритеты
```bash
gh label create "priority:critical" --color "B60205" --description "Critical priority" --repo shakirena/gold
gh label create "priority:high"     --color "D93F0B" --description "High priority" --repo shakirena/gold
gh label create "priority:medium"   --color "FBCA04" --description "Medium priority" --repo shakirena/gold
gh label create "priority:low"      --color "0E8A16" --description "Low priority" --repo shakirena/gold
```

#### Размеры
```bash
gh label create "size:xs" --color "C2E0C6" --description "< 2 hours" --repo shakirena/gold
gh label create "size:s"  --color "C2E0C6" --description "Half day" --repo shakirena/gold
gh label create "size:m"  --color "FEF2C0" --description "1-2 days" --repo shakirena/gold
gh label create "size:l"  --color "F9D0C4" --description "3-5 days" --repo shakirena/gold
```

#### Компоненты
```bash
gh label create "component:backend"  --color "BFD4F2" --description "Backend" --repo shakirena/gold
gh label create "component:frontend" --color "BFD4F2" --description "Frontend" --repo shakirena/gold
gh label create "component:ai"       --color "BFD4F2" --description "AI/ML" --repo shakirena/gold
gh label create "component:infra"    --color "BFD4F2" --description "Infrastructure" --repo shakirena/gold
gh label create "component:docs"     --color "BFD4F2" --description "Documentation" --repo shakirena/gold
```

#### Статусы QA / Security / Deploy
```bash
gh label create "qa:in-progress" --color "EDEDED" --description "QA in progress" --repo shakirena/gold
gh label create "qa:passed"      --color "0E8A16" --description "QA passed" --repo shakirena/gold
gh label create "qa:failed"      --color "D73A4A" --description "QA failed" --repo shakirena/gold
gh label create "security:passed" --color "0E8A16" --description "Security review passed" --repo shakirena/gold
gh label create "security:failed" --color "B60205" --description "Security review failed" --repo shakirena/gold
gh label create "deployed:staging"    --color "BFD4F2" --description "Deployed to staging" --repo shakirena/gold
gh label create "deployed:production" --color "006B75" --description "Deployed to production" --repo shakirena/gold
```

### Шаг 2: Создать GitHub Project Board

```bash
# Создать проект
gh project create --owner shakirena --title "Gold Kanban Board" --format json
```

Запомни `projectId` из ответа.

Добавить поле Status с вариантами (через GraphQL):
```bash
gh api graphql -f query='
mutation {
  addProjectV2Field(input: {
    projectId: "{projectId}",
    dataType: SINGLE_SELECT,
    name: "Status"
  }) { projectV2Field { id } }
}'
```

### Шаг 3: Итоговый отчёт

```
✅ SETUP COMPLETE

Labels созданы: 35
Project Board: https://github.com/orgs/shakirena/projects/{id}

Следующий шаг: /init для проверки всей системы, или /feature <описание> для первой задачи.
```
