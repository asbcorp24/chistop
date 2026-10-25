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

Ниже описана установка Chistop на обычный виртуальный хостинг Timeweb (не VPS).

## 1. Требования

Перед установкой в панели Timeweb выберите для сайта PHP 8.2 или новее. Для проекта также потребуется MySQL и SSH-доступ с возможностью запуска Composer.

Рекомендуемые PHP-расширения: BCMath, Ctype, cURL, DOM/XML, Fileinfo, Intl, Mbstring, OpenSSL, PDO, PDO MySQL, Tokenizer и XML.

## 2. Создание базы данных

В панели Timeweb создайте новую базу MySQL и отдельного пользователя базы данных.

Сохраните:

- имя базы данных;
- имя пользователя;
- пароль;
- адрес сервера MySQL, указанный Timeweb.

Эти значения понадобятся для файла `.env`.

## 3. Загрузка проекта

Подключитесь к хостингу по SSH и перейдите в каталог, в котором будет находиться приложение.

Пример:

```bash
cd ~
git clone https://github.com/asbcorp24/chistop.git
cd chistop
```

Для последующих обновлений:

```bash
cd ~/chistop
git pull origin main
```

Если Git на тарифе недоступен, проект можно загрузить архивом через файловый менеджер Timeweb, но установка через Git предпочтительнее.

## 4. Установка PHP-зависимостей

В каталоге проекта выполните:

```bash
composer install --no-dev --optimize-autoloader
```

Если команда `composer` недоступна напрямую, используйте способ запуска Composer, предоставленный вашим тарифом Timeweb.

## 5. Создание .env

Скопируйте пример конфигурации:

```bash
cp .env.example .env
```

Откройте `.env` и укажите реальные параметры сайта и базы данных:

```dotenv
APP_NAME=Chistop
APP_ENV=production
APP_DEBUG=false
APP_URL=https://college.example.ru

DB_CONNECTION=mysql
DB_HOST=ИМЯ_СЕРВЕРА_MYSQL
DB_PORT=3306
DB_DATABASE=ИМЯ_БАЗЫ
DB_USERNAME=ПОЛЬЗОВАТЕЛЬ_БАЗЫ
DB_PASSWORD=ПАРОЛЬ_БАЗЫ

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

На рабочем сайте обязательно оставляйте `APP_DEBUG=false`.

## 6. Генерация ключа Laravel

Выполните:

```bash
php artisan key:generate
```

## 7. Корневая директория сайта

Это важный пункт.

Домен должен смотреть не на корень репозитория `chistop`, а на каталог:

```text
chistop/public
```

В настройках сайта/домена Timeweb укажите каталог `public` проекта как корневую директорию сайта.

Файл `.env`, каталог `vendor`, исходный код приложения и остальные служебные файлы Laravel не должны быть доступны непосредственно из интернета.

## 8. Миграции базы данных

После настройки `.env` выполните:

```bash
php artisan migrate --force
```

Для первоначального заполнения системных данных:

```bash
php artisan db:seed --force
```

При текущей начальной конфигурации создается администратор:

```text
Логин: admin@chistop.local
Пароль: ChangeMe123!
```

Сразу после первого входа пароль необходимо изменить. На production-системе не оставляйте пароль по умолчанию.

## 9. Публичное хранилище

Выполните:

```bash
php artisan storage:link
```

Это создаст ссылку `public/storage` на `storage/app/public`.

## 10. Права на каталоги

Laravel должен иметь возможность записывать в:

```text
storage/
bootstrap/cache/
```

Не устанавливайте без необходимости права `777`. Используйте штатные права пользователя виртуального хостинга.

## 11. Очистка и оптимизация

После установки или обновления приложения выполните:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 12. Планировщик Laravel

Когда в Chistop появятся фоновые регламентные задания, в панели Timeweb необходимо добавить Cron-задачу, запускаемую каждую минуту:

```bash
cd /ПОЛНЫЙ/ПУТЬ/chistop && php artisan schedule:run
```

Точный абсолютный путь зависит от аккаунта Timeweb.

## 13. Очереди

Chistop использует очередь для длительных операций, в том числе крупных импортов XML.

На виртуальном хостинге, где постоянный `queue:work` может быть недоступен, можно запускать обработку очереди через Cron:

```bash
cd /ПОЛНЫЙ/ПУТЬ/chistop && php artisan queue:work --stop-when-empty --tries=3
```

Например, раз в минуту. Если тариф позволяет постоянно работающие процессы, предпочтительнее использовать обычный Laravel queue worker.

## 14. Проверка установки

Откройте:

```text
https://ВАШ-ДОМЕН/up
```

Laravel должен вернуть успешный ответ.

После этого откройте:

```text
https://ВАШ-ДОМЕН/admin
```

Должна появиться страница входа Filament.

## 15. Обновление Chistop

После новых коммитов в `main`:

```bash
cd ~/chistop
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Если обновление содержит новые frontend-зависимости или собранные assets, дополнительно выполните действия, указанные в примечании к конкретному релизу.

## 16. Если после установки ошибка 500

Сначала временно посмотрите журнал Laravel:

```bash
tail -n 100 storage/logs/laravel.log
```

Проверьте:

1. выбранную версию PHP;
2. наличие `vendor/`;
3. наличие `APP_KEY`;
4. параметры MySQL в `.env`;
5. права записи в `storage` и `bootstrap/cache`;
6. что корень домена указывает именно на `public`.

После исправления выполните:

```bash
php artisan optimize:clear
```
