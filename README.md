# Chistop

Информационная система колледжа на Laravel + Filament.

## Архитектурный принцип

Расписание и педагогическая нагрузка импортируются из XML и синхронизируются с внутренними сущностями системы. Chistop не формирует их вручную.

## Стек

- PHP 8.2+
- Laravel 12
- Filament 4
- MySQL 8
- Livewire / Alpine.js
- AJAX-интерфейсы
- адаптивный desktop/mobile UI

## Основные контуры

Организация и RBAC, контингент, учебная часть, XML-интеграция, журнал и посещаемость, контроль успеваемости, экзамены и пересдачи, ГИА и выпуск, практика, приемная комиссия, воспитательная и методическая работа, кадры, общежитие, стипендии, документы, отчетность и аудит.

# Установка на виртуальный хостинг Timeweb

Инструкция ниже проверена для структуры виртуального хостинга Timeweb, где системная команда `php` в SSH может указывать на старый PHP 8.0. Chistop использует Laravel 12 и требует PHP 8.2+, поэтому в примерах принудительно используется PHP 8.3:

```text
/opt/php8.3/bin/php
```

## 1. Проверка PHP

Подключитесь по SSH и выполните:

```bash
php -v
which php
ls -d /opt/php*
/opt/php8.3/bin/php -v
```

Если обычный `php -v` показывает PHP 8.0 — это не проблема. Для Chistop не используйте эту команду. Все команды Artisan и Composer ниже запускаются через `/opt/php8.3/bin/php`.

В панели Timeweb для самого сайта также выберите PHP 8.3.

## 2. Создание базы MySQL

В панели Timeweb создайте базу MySQL и пользователя. Сохраните имя базы, имя пользователя, пароль и адрес сервера MySQL — они понадобятся в `.env`.

## 3. Размещение проекта

В текущей установке проект можно разместить так:

```text
~/Chist/public_html
```

Пример клонирования:

```bash
mkdir -p ~/Chist
cd ~/Chist
git clone https://github.com/asbcorp24/chistop.git public_html
cd ~/Chist/public_html
```

Если репозиторий уже склонирован и приглашение SSH выглядит примерно как:

```text
~/Chist/public_html$
```

повторно клонировать ничего не нужно.

## 4. Установка зависимостей

ВАЖНО: не запускайте здесь просто `composer install`, потому что Composer возьмет PHP 8.0 из окружения Timeweb.

Находясь в `~/Chist/public_html`, выполните:

```bash
/opt/php8.3/bin/php $(which composer) install --no-dev --optimize-autoloader
```

Проверить Composer можно так:

```bash
/opt/php8.3/bin/php $(which composer) --version
```

## 5. Создание .env

```bash
cd ~/Chist/public_html
cp .env.example .env
```

Заполните:

```dotenv
APP_NAME=Chistop
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ВАШ-ДОМЕН

DB_CONNECTION=mysql
DB_HOST=СЕРВЕР_MYSQL_TIMEWEB
DB_PORT=3306
DB_DATABASE=ИМЯ_БАЗЫ
DB_USERNAME=ПОЛЬЗОВАТЕЛЬ
DB_PASSWORD=ПАРОЛЬ

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

## 6. Ключ Laravel

```bash
/opt/php8.3/bin/php artisan key:generate
```

## 7. Корень домена — обязательно /public

Сам проект находится здесь:

```text
~/Chist/public_html
```

Но корневой каталог сайта в панели Timeweb должен быть:

```text
~/Chist/public_html/public
```

То есть браузер должен видеть только Laravel-каталог `public`.

Нельзя направлять домен непосредственно на `~/Chist/public_html`: в таком случае служебные файлы Laravel могут оказаться доступны извне.

## 8. База данных

После заполнения `.env`:

```bash
cd ~/Chist/public_html
/opt/php8.3/bin/php artisan migrate --force
/opt/php8.3/bin/php artisan db:seed --force
```

Начальный администратор:

```text
Логин: admin@chistop.local
Пароль: ChangeMe123!
```

После первого входа пароль необходимо сменить.

## 9. Storage

```bash
/opt/php8.3/bin/php artisan storage:link
```

Laravel должен иметь права записи в:

```text
storage/
bootstrap/cache/
```

Права `777` без необходимости не устанавливайте.

## 10. Оптимизация

```bash
/opt/php8.3/bin/php artisan optimize:clear
/opt/php8.3/bin/php artisan config:cache
/opt/php8.3/bin/php artisan route:cache
/opt/php8.3/bin/php artisan view:cache
```

## 11. Проверка

После настройки домена откройте:

```text
https://ВАШ-ДОМЕН/up
```

Затем:

```text
https://ВАШ-ДОМЕН/admin
```

На `/admin` должна открыться авторизация Filament.

## 12. Обновление из main

Для установки наших следующих изменений:

```bash
cd ~/Chist/public_html
git pull origin main
/opt/php8.3/bin/php $(which composer) install --no-dev --optimize-autoloader
/opt/php8.3/bin/php artisan migrate --force
/opt/php8.3/bin/php artisan optimize:clear
/opt/php8.3/bin/php artisan config:cache
/opt/php8.3/bin/php artisan route:cache
/opt/php8.3/bin/php artisan view:cache
```

## 13. Cron и очереди

Планировщик Laravel:

```bash
cd /ПОЛНЫЙ/ПУТЬ/Chist/public_html && /opt/php8.3/bin/php artisan schedule:run
```

Очередь для XML-импортов и других фоновых задач:

```bash
cd /ПОЛНЫЙ/ПУТЬ/Chist/public_html && /opt/php8.3/bin/php artisan queue:work --stop-when-empty --tries=3
```

На виртуальном хостинге эти команды можно назначить через Cron.

## 14. Диагностика

Если Composer снова пишет:

```text
your php version (8.0.30) does not satisfy that requirement
```

значит Composer был запущен обычной командой `composer`. Используйте:

```bash
/opt/php8.3/bin/php $(which composer) install --no-dev --optimize-autoloader
```

Если сайт возвращает ошибку 500:

```bash
cd ~/Chist/public_html
tail -n 100 storage/logs/laravel.log
/opt/php8.3/bin/php artisan optimize:clear
```

Проверьте `APP_KEY`, параметры MySQL, права `storage/` и `bootstrap/cache/`, а также что домен направлен именно на `~/Chist/public_html/public`.
