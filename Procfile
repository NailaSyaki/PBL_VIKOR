web: composer install --optimize-autoloader --no-dev && php artisan key:generate && php artisan migrate --force && npm install && npm run build && php artisan serve --host=0.0.0.0 --port=${PORT}
