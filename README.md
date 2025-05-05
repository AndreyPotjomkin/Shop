 Laravel Shop API

Установка и настройка

Установите зависимости:
composer install

Настройте окружение:
cp .env.example .env
php artisan key:generate

Настройте подключение к БД в файле .env

Работа с базой данных

Применить миграции:
php artisan migrate

Тестирование

Запуск всех тестов:
php artisan test
