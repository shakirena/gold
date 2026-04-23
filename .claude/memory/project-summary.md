# Gold — Project Summary

Система управления кредитами, займами и финансовыми операциями.

**Repo:** shakirena/gold | **Branch:** master | **Local:** D:\OSPanel\domains\gold

## Стек
- Framework: Yii2 Basic, PHP 7.1+
- БД: MySQL 5.7, dbname: `gold`
- Frontend: Bootstrap 3, jQuery, Kartik widgets (GridView, Select2, DatePicker)
- Тесты: Codeception 2.3
- Контейнер: Docker (yiisoftware/yii2-php:7.1-apache), порт 8000

## Ключевые модули
| Контроллер | Модель | Назначение |
|-----------|--------|-----------|
| ClientController | Client | Клиенты |
| CreditController | Credit | Кредиты/займы |
| PaymentController | Payment | Платежи |
| FineController | Fine | Штрафы |
| KassaController | Kassa | Касса/транзакции |
| ProductsController | Products | Продукты/тарифы |
| StoreController | Store | Склад |

## Coding conventions
- PHP PSR-2, namespace: `app\*`
- Таблицы: snake_case. Классы: PascalCase
- Bootstrap 3: `.col-md-*`, `.btn-default`
- Миграции: `php yii migrate/create`
- Тесты: `php vendor/bin/codecept run`
