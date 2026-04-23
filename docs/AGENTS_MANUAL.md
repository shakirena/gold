.33333333.	# Мануал по работе с Agents Framework

Система мультиагентной разработки для проекта Gold. Агенты автоматизируют весь pipeline — от создания задачи до деплоя — через 6 обязательных Quality Gates.

---

## Содержание

1. [Быстрый старт](#1-быстрый-старт)
2. [Как это работает — общая схема](#2-как-это-работает--общая-схема)
3. [Все команды](#3-все-команды)
4. [Kanban flow — жизненный цикл задачи](#4-kanban-flow--жизненный-цикл-задачи)
5. [Quality Gates — что проверяется на каждом шаге](#5-quality-gates--что-проверяется-на-каждом-шаге)
6. [Все агенты — кто что делает](#6-все-агенты--кто-что-делает)
7. [Карта файлов — где что лежит](#7-карта-файлов--где-что-лежит)
8. [Memory система — как агенты помнят контекст](#8-memory-система--как-агенты-помнят-контекст)
9. [Labels — система меток](#9-labels--система-меток)
10. [Частые вопросы и решения](#10-частые-вопросы-и-решения)

---

## 1. Быстрый старт

### Первый запуск (один раз)

```
/init           — проверить окружение, создать папки, инициализировать memory
/setup-board    — создать 35 GitHub-меток и Project Board
```

### Обычный рабочий процесс

```
/feature добавить экспорт в PDF    — создать задачу в GitHub
/analyze #5                         — декомпозиция: spec + архитектура
/develop #5                         — разработка + security review
/test #5                            — тесты + TC документация
/deploy staging                     — деплой на staging/
```

### Автоматический режим

```
/kanban добавить экспорт в PDF      — полный pipeline от описания до деплоя
/sweep                              — обработать всю доску автоматически
/board                              — посмотреть состояние доски
```

---

## 2. Как это работает — общая схема

```
ТЫ (человек)
    │
    │  /feature  /analyze  /develop  /test  /deploy  /kanban  /sweep ...
    ▼
Claude Code (оркестр)
    │  Разбирает команды, запускает Team Leads
    │
    ├── analysis-lead ──► analyst + architect
    │       G1, G2
    │
    ├── dev-lead ────────► developer + security-reviewer
    │       G3, G4
    │
    ├── qa-lead ─────────► tester + test-case-writer
    │       G5
    │
    └── ops-lead ────────► devops
            G6
```

**Два уровня агентов:**

| Уровень | Кто | Что делает |
|---------|-----|-----------|
| **Team Leads** | analysis-lead, dev-lead, qa-lead, ops-lead | Координируют, проверяют Quality Gates, двигают задачи по доске |
| **Рабочие агенты** | analyst, architect, developer, security-reviewer, tester, test-case-writer, e2e-tester, devops | Выполняют конкретные задачи: пишут код, тесты, документацию |

**Главное правило:** Ты вызываешь Team Lead командой → он сам нанимает нужных рабочих агентов.

---

## 3. Все команды

### Pipeline — основные команды

#### `/feature <описание>`
Создаёт GitHub Issue для новой фичи или бага.

```
/feature добавить фильтрацию клиентов по дате рождения
/feature не работает расчёт штрафа при просрочке
```

Что происходит:
- Анализирует описание, определяет тип (feature/bug), приоритет, размер
- Создаёт issue с шаблоном: Описание + Бизнес-ценность + Acceptance Criteria
- Выставляет labels: `type:feature`, `kanban:backlog`, `priority:*`, `size:*`
- Выводит ссылку на созданный issue

---

#### `/analyze #N`
Декомпозирует фичу на User Stories, создаёт спецификацию и архитектуру.

```
/analyze #5
/analyze #5 #6 #7    — несколько issues параллельно
```

Что происходит:
- **G1 Gate:** проверяет что issue готов к анализу
- **Параллельно** запускает analyst (пишет spec) и architect (пишет arch doc + code stubs)
- **G2 Gate:** проверяет что spec и arch doc полные, stories по INVEST
- Двигает issue: `backlog → analysis → ready-for-dev`

Артефакты на выходе:
- `docs/specs/feature-N-name.md` — функциональные требования, сценарии
- `docs/arch/feature-N-name.md` — ERD, API контракты, архитектурные решения
- Дочерние issues (User Stories) в GitHub
- `.claude/memory/stories/story-N.md` — сжатый контекст для следующих агентов

---

#### `/develop #N`
Реализует фичу: пишет код, unit-тесты, проводит security review.

```
/develop #5
/develop #5 #6 #7    — несколько issues (параллельно если независимы)
```

Что происходит:
- Проверяет зависимости (нет ли open blockers)
- Проверяет WIP лимит (не более 5 задач одновременно)
- **G3 Gate:** build OK, AC testable, deps resolved
- **developer** реализует код в отдельной git ветке `feature/N-name` (worktree isolation)
- **G4 Gate:** build + lint + unit тесты
- **security-reviewer** проверяет только изменённые файлы (OWASP Top 10)
- Двигает issue: `ready-for-dev → in-development → testing`

Артефакты на выходе:
- Код в ветке `feature/N-name` (готов к merge)
- Unit-тесты в `tests/unit/`
- Labels: `security:passed` или `security:failed`

---

#### `/test #N`
Пишет unit-тесты (coverage ≥ 95%) и создаёт TC документацию.

```
/test #5
/test all    — все issues в колонке testing
```

Что происходит:
- Проверяет наличие `security:passed` (без него — BLOCKED)
- **Параллельно:** tester (unit тесты) + test-case-writer (TC документация)
- **G5 Gate:** coverage ≥ 95%, AC верифицированы тестами
- Двигает issue: `testing → ready-to-deploy`

Артефакты на выходе:
- Тесты в `tests/unit/`
- `docs/test-cases/feature-N-name.md` — тест-кейсы
- Обновлённый `docs/test-cases/traceability-tc.md`
- Labels: `qa:passed` или `qa:failed`

---

#### `/deploy staging|production|all`
Деплоит на staging или production.

```
/deploy staging       — деплой всего из ready-to-deploy
/deploy #5            — деплой конкретного issue
/deploy production    — production (требует APPROVE PRODUCTION DEPLOY от тебя)
```

Что происходит:
- **G6 Pre-flight:** проверяет `qa:passed` + `security:passed` + build
- **devops** выполняет деплой, health check, smoke test
- **G6 Post-deploy:** health 200, логи чистые
- При падении health — немедленный rollback
- Двигает issue: `ready-to-deploy → done`, закрывает issue

---

#### `/e2e #N`
Создаёт E2E acceptance тесты из TC документации.

```
/e2e #5
/e2e all
```

Что происходит:
- Читает `docs/test-cases/feature-N-name.md`
- Создаёт Page Objects в `tests/_support/Page/`
- Создаёт Acceptance тесты в `tests/acceptance/`
- Critical + High тест-кейсы автоматизируются обязательно
- **Не блокирует деплой** — это отдельный поток

---

### Автоматизация

#### `/kanban <описание>`
Полный pipeline от описания до деплоя — все 6 gates.

```
/kanban добавить экспорт клиентов в Excel
```

Запускает последовательно: `/feature` → `/analyze` → `/develop` → `/test` → `/deploy staging`. При блокировке на любом gate останавливается и сообщает причину.

---

#### `/sweep [dry-run]`
Обрабатывает всю доску **справа налево** (порядок гарантирован).

```
/sweep              — обработать всё
/sweep dry-run      — показать план, не выполнять
/sweep testing      — обработать только колонку testing
```

Порядок: `ready-to-deploy → testing → ready-for-dev`. Безопасный вариант — нет race conditions.

---

#### `/swarm [develop|test|analyze|dry-run]`
Все Team Leads **параллельно** — максимальная скорость.

```
/swarm              — всё параллельно
/swarm develop      — только dev-lead для ready-for-dev issues
/swarm dry-run      — показать план
```

Быстрее чем sweep, но возможны race conditions на labels. Используй когда issues независимы.

---

### Навигация

#### `/board`
Показывает текущее состояние доски.

```
/board
/board blocked      — только заблокированные
```

Выводит: все колонки с issues, WIP счётчики (N/5), блокировки, метрики.

---

#### `/pick [колонка]`
Предлагает следующую задачу по приоритету (critical → high → medium → low).

```
/pick                   — следующая из ready-for-dev
/pick ready-to-deploy   — следующая к деплою
/pick backlog           — следующая для анализа
```

Пропускает заблокированные issues (нет смысла брать если blocker открыт).

---

### Настройка (один раз)

| Команда | Когда использовать |
|---------|-------------------|
| `/init` | При старте проекта — проверить окружение, создать структуру папок и memory файлы |
| `/setup-board` | Один раз — создать 35 GitHub меток + Project Board |
| `/refresh-agents` | Когда нужно обновить агентов из другого проекта |

---

## 4. Kanban flow — жизненный цикл задачи

```
backlog → analysis → ready-for-dev → in-development → testing → ready-to-deploy → done
```

| Колонка | Label | Кто переводит | Что происходит |
|---------|-------|---------------|----------------|
| **Backlog** | `kanban:backlog` | Ты / `/feature` | Новые идеи и баги. Issue создан, не начат |
| **Analysis** | `kanban:analysis` | analysis-lead (G1 PASS) | analysis-lead с analyst + architect работают над spec |
| **Ready for Dev** | `kanban:ready-for-dev` | analysis-lead (G2 PASS) | Spec готов, архитектура готова, можно разрабатывать |
| **In Development** | `kanban:in-development` | dev-lead (G3 PASS) | developer пишет код в ветке feature/N |
| **Testing** | `kanban:testing` | dev-lead (G4 PASS + security:passed) | qa-lead пишет тесты и TC документацию |
| **Ready to Deploy** | `kanban:ready-to-deploy` | qa-lead (G5 PASS) | Всё проверено, ждёт деплоя |
| **Done** | `kanban:done` | ops-lead (G6 PASS) | Задеплоено, issue закрыт |

**Главное правило:** Агент сначала обновляет статус (label), потом начинает работу. Так всегда видно реальное состояние доски.

---

## 5. Quality Gates — что проверяется на каждом шаге

Gates — обязательные контрольные точки. Без PASS агент не может двигать задачу дальше.

### G1: backlog → analysis
**Кто проверяет:** analysis-lead

- Есть описание бизнес-ценности (зачем?)
- Есть хотя бы 1 Acceptance Criteria
- Нет дубликатов
- Выставлены: тип (`type:*`), приоритет (`priority:*`), размер (`size:*`)

### G2: analysis → ready-for-dev
**Кто проверяет:** analysis-lead

- Создан `docs/specs/feature-N-name.md` (с FR, NFR, Given/When/Then)
- Создан `docs/arch/feature-N-name.md` (ERD, API, code stubs)
- Каждая User Story — ровно ONE Given/When/Then (SD-1)
- Нет `size:xl` stories, нет " и " / " and " в заголовках (SD-3, SD-5)

### G3: ready-for-dev → in-development
**Кто проверяет:** dev-lead

- `composer install` без ошибок
- Зависимости разрешены (нет открытых blockers)
- WIP In Development < 5

### G4: in-development → testing
**Кто проверяет:** dev-lead

- `php vendor/bin/codecept run unit` — GREEN
- `composer validate` — OK
- Написаны unit-тесты для новых классов
- Нет `.bak` файлов, нет `TODO`/`FIXME` в новом коде
- **`security:passed` выставлен security-reviewer**

### G5: testing → ready-to-deploy
**Кто проверяет:** qa-lead

- Coverage ≥ 95% новых файлов (для hotfix: ≥ 50%)
- Все AC верифицированы тестами
- `docs/test-cases/feature-N-name.md` создан
- `security:passed` присутствует (qa-lead только проверяет, не ставит!)

### G6: ready-to-deploy → done
**Кто проверяет:** ops-lead

- Все тесты green
- Health check: HTTP 200
- Smoke test основных страниц
- Логи чистые (нет ERROR/WARN в первые 60 сек)

### Verdicts

| Вердикт | Что значит | Действие |
|---------|-----------|----------|
| **PASS ✅** | Все проверки прошли | Переход в следующую колонку |
| **NEEDS WORK ⚠️** | Мелкие проблемы | Агент исправляет inline, повторяет gate |
| **BLOCKED 🚫** | Критическая проблема | СТОП, issue остаётся, агент пишет причину в комментарий |

---

## 6. Все агенты — кто что делает

### Team Leads (координация)

#### `analysis-lead`
**Файл:** `.claude/agents/analysis-lead.md`  
**Вызывается через:** `/analyze`  
**Модель:** Sonnet

Управляет фазой анализа. Запускает analyst и architect параллельно. Проверяет G1 и G2. Двигает задачу из backlog через analysis в ready-for-dev. Пишет Quality Gate Reports в комментарии к issue.

#### `dev-lead`
**Файл:** `.claude/agents/dev-lead.md`  
**Вызывается через:** `/develop`  
**Модель:** Sonnet

Управляет разработкой. Проверяет зависимости и WIP лимит. Запускает developer. После кода — запускает security-reviewer синхронно (ждёт результата). Проверяет G3 и G4. Двигает из ready-for-dev через in-development в testing.

#### `qa-lead`
**Файл:** `.claude/agents/qa-lead.md`  
**Вызывается через:** `/test`  
**Модель:** Sonnet

Управляет тестированием. Блокирует работу если нет `security:passed`. Запускает tester и test-case-writer параллельно. Проверяет G5. Двигает из testing в ready-to-deploy (или обратно в in-development при провале).

#### `ops-lead`
**Файл:** `.claude/agents/ops-lead.md`  
**Вызывается через:** `/deploy`  
**Модель:** Sonnet

Управляет деплоем. Проверяет G6 Pre-flight (qa:passed + security:passed). Запускает devops. После деплоя — health check и smoke test. При падении — немедленный rollback. Production деплой требует `APPROVE PRODUCTION DEPLOY` от тебя.

---

### Рабочие агенты (исполнение)

#### `analyst`
**Файл:** `.claude/agents/analyst.md`  
**Запускается:** analysis-lead  
**Модель:** Sonnet

Декомпозирует фичу на User Stories по правилам SD-1..SD-5. Каждая story — один Given/When/Then, не более 3 дней работы. Создаёт:
- `docs/specs/feature-N-name.md` — функциональные и нефункциональные требования
- Дочерние issues в GitHub (User Stories)
- Раздел 📋 в `.claude/memory/stories/story-N.md`

#### `architect`
**Файл:** `.claude/agents/architect.md`  
**Запускается:** analysis-lead (параллельно с analyst)  
**Модель:** Opus (высокое качество архитектурных решений)

Проектирует техническое решение. Создаёт:
- `docs/arch/feature-N-name.md` — ERD, API контракты, ADR (архитектурные решения), code stubs
- Раздел 🏗️ в `.claude/memory/stories/story-N.md`

#### `developer`
**Файл:** `.claude/agents/developer.md`  
**Запускается:** dev-lead  
**Модель:** Opus (высокое качество кода)  
**Isolation:** worktree (отдельная git ветка)

Реализует backend и frontend по spec и arch doc. Пишет unit-тесты для service layer. Делает build verification перед передачей. Коммитит с сообщением `feat(#N): description`.

#### `security-reviewer`
**Файл:** `.claude/agents/security-reviewer.md`  
**Запускается:** dev-lead (после developer)  
**Модель:** Sonnet

Проверяет **только изменённые файлы** (delta scope). Проверяет OWASP Top 10, JWT/RBAC, специфику Yii2 (mass assignment, SQL injection через ActiveRecord). **Только он** выставляет `security:passed` или `security:failed`. Максимум 3 цикла исправлений до эскалации.

#### `tester`
**Файл:** `.claude/agents/tester.md`  
**Запускается:** qa-lead  
**Модель:** Sonnet

Пишет **только unit-тесты** (не E2E). Coverage ≥ 95% новых файлов. Время выполнения < 2 минут. Мокает всё внешнее (БД, API). Использует Codeception.

#### `test-case-writer`
**Файл:** `.claude/agents/test-case-writer.md`  
**Запускается:** qa-lead (параллельно с tester)  
**Модель:** Sonnet

Пишет TC документацию из spec + arch + AC. Для каждого AC: happy path + error case + RBAC тест. Обновляет `docs/test-cases/traceability-tc.md`.

#### `e2e-tester`
**Файл:** `.claude/agents/e2e-tester.md`  
**Запускается:** через `/e2e` команду (отдельный поток)  
**Модель:** Opus

Пишет Acceptance тесты (Codeception). Создаёт Page Objects в `tests/_support/Page/`. Автоматизирует Critical + High TCs обязательно. Не блокирует основной pipeline.

#### `devops`
**Файл:** `.claude/agents/devops.md`  
**Запускается:** ops-lead  
**Модель:** Sonnet

Выполняет деплой (Docker / PHP команды из CLAUDE.md), health check, smoke test, проверяет логи. Документирует rollback команду перед деплоем.

#### `doc-sync`
**Файл:** `.claude/agents/doc-sync.md`  
**Запускается:** analysis-lead и dev-lead после своей работы  
**Модель:** Sonnet

Обновляет матрицу трассируемости (`docs/traceability.md`). Синхронизирует spec↔code. Запускается автоматически, не через команду вручную.

---

### Shared протоколы (не агенты, а инструкции для Team Leads)

#### `quality-gates.md`
**Файл:** `.claude/agents/quality-gates.md`  
Детальные чеклисты для G1–G6. Team Leads читают этот файл чтобы знать что проверять на каждом gate.

#### `kanban-board-sync.md`
**Файл:** `.claude/agents/kanban-board-sync.md`  
Протокол синхронизации GitHub Labels + Project Board. Team Leads читают для правильного обновления статусов.

#### `dependency-resolver.md`
**Файл:** `.claude/agents/dependency-resolver.md`  
Алгоритм разрешения DAG-зависимостей между issues (синтаксис: `Blocked by: #N`). Читают dev-lead и qa-lead перед запуском.

---

## 7. Карта файлов — где что лежит

```
gold/
│
├── CLAUDE.md                          # Главные инструкции проекта (стек, команды, правила)
│
├── .claude/
│   ├── AGENTS_FRAMEWORK.md            # Полное описание архитектуры системы агентов
│   ├── settings.json                  # Настройки Claude Code (permissions, hooks)
│   │
│   ├── agents/                        # Инструкции для агентов
│   │   ├── analysis-lead.md           # Team Lead: анализ (G1, G2)
│   │   ├── dev-lead.md                # Team Lead: разработка (G3, G4)
│   │   ├── qa-lead.md                 # Team Lead: тестирование (G5)
│   │   ├── ops-lead.md                # Team Lead: деплой (G6)
│   │   ├── analyst.md                 # Рабочий: декомпозиция → User Stories + spec
│   │   ├── architect.md               # Рабочий: архитектура → ERD, API, stubs
│   │   ├── developer.md               # Рабочий: backend + frontend код (Opus, worktree)
│   │   ├── security-reviewer.md       # Рабочий: OWASP review → security:passed/failed
│   │   ├── tester.md                  # Рабочий: unit тесты (coverage ≥ 95%)
│   │   ├── test-case-writer.md        # Рабочий: TC документация
│   │   ├── e2e-tester.md              # Рабочий: Acceptance тесты (Opus)
│   │   ├── devops.md                  # Рабочий: деплой, health check, rollback
│   │   ├── doc-sync.md                # Рабочий: traceability matrix (авто)
│   │   ├── quality-gates.md           # Протокол: чеклисты G1–G6
│   │   ├── kanban-board-sync.md       # Протокол: синхронизация GitHub labels + board
│   │   └── dependency-resolver.md     # Протокол: разрешение DAG-зависимостей
│   │
│   ├── commands/                      # Команды (/feature, /analyze, ...)
│   │   ├── feature.md                 # /feature — создать GitHub Issue
│   │   ├── analyze.md                 # /analyze — запустить analysis-lead
│   │   ├── develop.md                 # /develop — запустить dev-lead
│   │   ├── test.md                    # /test — запустить qa-lead
│   │   ├── deploy.md                  # /deploy — запустить ops-lead
│   │   ├── e2e.md                     # /e2e — запустить e2e-tester
│   │   ├── board.md                   # /board — показать Kanban доску
│   │   ├── pick.md                    # /pick — следующая задача по приоритету
│   │   ├── sweep.md                   # /sweep — автопилот right-to-left
│   │   ├── swarm.md                   # /swarm — все Team Leads параллельно
│   │   ├── kanban.md                  # /kanban — полный pipeline
│   │   ├── setup-board.md             # /setup-board — создать labels + board
│   │   ├── init.md                    # /init — инициализация системы
│   │   └── refresh-agents.md          # /refresh-agents — обновить агентов из источника
│   │
│   ├── memory/                        # Persistent memory агентов
│   │   ├── project-summary.md         # Сжатый контекст проекта (~500 токенов)
│   │   ├── active-sprint.md           # Текущий WIP: In Development / Testing
│   │   ├── decisions.md               # Кросс-story архитектурные решения
│   │   └── stories/
│   │       ├── _template.md           # Шаблон для новых stories
│   │       └── story-N.md             # Накопленный контекст по story (📋+🏗️+💻+🔒+✅)
│   │
│   └── handoffs/                      # Передачи между агентами
│       └── story-N-dev.md             # Handoff от developer к QA (список файлов, что сделано)
│
├── docs/                              # Артефакты разработки
│   ├── specs/
│   │   └── feature-N-name.md          # Спецификации (FR, NFR, Given/When/Then)
│   ├── arch/
│   │   └── feature-N-name.md          # Архитектурные решения (ERD, API, ADR, stubs)
│   ├── test-cases/
│   │   ├── feature-N-name.md          # Тест-кейсы (happy path, error, RBAC)
│   │   └── traceability-tc.md         # Матрица: AC → тест-кейсы → тесты → E2E
│   ├── traceability.md                # Общая матрица трассируемости
│   └── AGENTS_MANUAL.md               # Этот мануал
│
└── tests/
    ├── unit/                          # Unit тесты (Codeception)
    ├── functional/                    # Functional тесты
    ├── acceptance/                    # Acceptance/E2E тесты
    │   └── FeatureCest.php            # Создаёт e2e-tester
    └── _support/
        └── Page/
            └── FeaturePage.php        # Page Objects (создаёт e2e-tester)
```

---

## 8. Memory система — как агенты помнят контекст

Вместо того чтобы каждый агент читал весь ARCHITECTURE.md, spec, arch doc — они читают сжатые файлы из `.claude/memory/`. Экономит ~15 000 токенов на story.

### Файлы memory

**`project-summary.md`** (~500 токенов)  
Краткое описание проекта: стек, структура, ключевые паттерны. Заменяет полный CLAUDE.md для агентов которым не нужны детали.

**`active-sprint.md`**  
Список что сейчас In Development и Testing. Обновляют dev-lead и qa-lead при каждом переходе. Нужен для проверки WIP лимита.

**`decisions.md`**  
Архитектурные решения которые влияют на несколько stories (например: "для всех новых экранов используем Select2 вместо native select"). Пишут architect и developer.

**`stories/story-N.md`**  
Главный файл — накапливается по мере прохождения pipeline:

```
📋 Задача         — analyst пишет: что нужно сделать, AC, User Story
🏗️ Архитектура   — architect пишет: как реализовать, какие классы, API
💻 Реализация    — developer пишет: что реально сделано, список файлов
🔒 Security      — security-reviewer пишет: что проверено, замечания
✅ QA             — qa-lead пишет: coverage, что протестировано
```

Developer читает story-N.md вместо spec(3K) + arch(3K) + issue(2K) = **экономия 8K токенов**.

### Правила memory

- Агенты **дописывают** свой раздел — не перезаписывают чужие
- Память читается **первой** (до specs и arch)
- `decisions.md` — только решения влияющие на 2+ stories
- `active-sprint.md` обновляют только Team Leads

---

## 9. Labels — система меток

### Kanban статусы (только один активен одновременно)

| Label | Колонка |
|-------|---------|
| `kanban:backlog` | Backlog |
| `kanban:analysis` | Analysis |
| `kanban:ready-for-dev` | Ready for Dev |
| `kanban:in-development` | In Development |
| `kanban:testing` | Testing |
| `kanban:ready-to-deploy` | Ready to Deploy |
| `kanban:done` | Done |

### Типы задач

| Label | Когда |
|-------|-------|
| `type:feature` | Родительский issue фичи |
| `type:story` | User Story (дочерний issue) |
| `type:bug` | Баг |
| `type:hotfix` | Срочное исправление (пропускает G1/G2) |
| `type:tech-debt` | Технический долг |
| `type:spike` | Исследование |
| `type:epic` | Эпик (несколько features) |

### Приоритеты

`priority:critical` > `priority:high` > `priority:medium` > `priority:low`

### Размеры

| Label | Оценка |
|-------|--------|
| `size:xs` | < 2 часов |
| `size:s` | Полдня |
| `size:m` | 1–2 дня |
| `size:l` | 3–5 дней |

> `size:xl` запрещён для User Stories — нужно дробить.

### Статусы (выставляются агентами)

| Label | Кто ставит | Значение |
|-------|-----------|----------|
| `security:passed` | **только security-reviewer** | Security review пройден |
| `security:failed` | **только security-reviewer** | Найдены уязвимости |
| `qa:passed` | qa-lead | Тесты прошли, coverage OK |
| `qa:failed` | qa-lead | Тесты упали |
| `deployed:staging` | devops | Задеплоено на staging |
| `deployed:production` | devops | Задеплоено на production |

> **Важно:** `security:*` ставит только security-reviewer. qa-lead только **проверяет наличие** этой метки — никогда не ставит и не убирает сам.

---

## 10. Частые вопросы и решения

### Issue завис в колонке, агент не двигает

Посмотри комментарии к issue — там будет Quality Gate Report с причиной BLOCKED. Исправь проблему и запусти команду заново.

### BLOCKED: security:passed отсутствует

`/test` и `/deploy` требуют `security:passed`. Значит `/develop` не завершился успешно. Запусти `/develop #N` снова — security-reviewer пройдёт review и выставит метку.

### WIP ≥ 5, dev-lead не берёт задачу

Это нормально — WIP лимит защищает от перегрузки. Используй `/test` или `/deploy` чтобы завершить текущие задачи и освободить место.

### Зависимости: `Blocked by: #M`

Если в body issue написано `Blocked by: #M` — агент не начнёт работу пока issue #M не закрыт (не в `kanban:done`). Сначала закрой blocker.

### Hotfix: нужно срочно, пропускаем анализ

1. Создай issue с labels `type:hotfix`, `priority:critical`, `kanban:ready-for-dev` (вручную)
2. Запусти `/develop #N` — hotfix пропускает G1 и G2
3. Coverage порог снижен до 50% (но security review обязателен)

### Хочу добавить нового агента или изменить существующего

Файлы агентов в `.claude/agents/*.md` — обычные markdown. Редактируй напрямую. Если хочешь подтянуть агентов из другого проекта — используй `/refresh-agents <путь>`.

### Где смотреть что сейчас в работе

```
/board              — визуальная доска
/pick               — что взять следующим
```

Или напрямую в GitHub Issues с фильтром по label.
