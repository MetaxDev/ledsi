# Laravel + Inertia (Vue 3) + Sanctum — Tasks / Stats

Тестовое приложение:
- Регистрация и аутентификация пользователей через cookie-based сессию (Laravel `web`) + доступ к API через `auth:sanctum`
- CRUD задач пользователя
- Статистика по задачам (кэшируется на 60 секунд, сбрасывается при изменениях задач через Observer)
- Админ-режим: список пользователей и просмотр статистики по выбранному пользователю
- Redis используется как `CACHE_STORE`
- Внешняя API-интеграция: получение данных из публичного API при логине пользователя, кэширование результата в Redis (5 минут) и отображение на странице задач

## Стек

- Laravel (API + web routes)
- Inertia.js + Vue 3 + TypeScript
- PostgreSQL
- Redis (cache)
- Docker Compose (app + node + db + redis)

---

## Быстрый старт (Docker)

### 1) Запуск контейнеров

```
docker compose up -d --build
```

Контейнер app автоматически:

- выполнит composer install
- скопирует .env.example в .env
- сгенерирует APP_KEY
- выполнит миграции
- сделает сид тестовых юзеров
- запустит php artisan serve на 0.0.0.0:8000

Контейнер node запустит Vite dev server на 0.0.0.0:5173.

### URLs

- Laravel: http://localhost:8000
- Vite: http://localhost:5173

Фронт и бэк на разных портах, авторизация работает через cookie (Sanctum SPA).

### Тестовые юзеры

- Админ: mail - admin@test.ru pass - 1 
- Юзер: mail - user@test.ru pass - 1
#### Регистрация работает, можно сделать новых

## Примечания
- Axios instance находится в resources/js/api.ts и настроен с withCredentials: true.
- Для стабильной работы на localhost рекомендуется использовать один и тот же хост (например localhost), чтобы не путаться с cookie.

