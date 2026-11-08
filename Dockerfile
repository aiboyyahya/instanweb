[phases.setup]
install = "apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip git unzip curl"

[phases.install]
commands = ["composer install --no-dev --optimize-autoloader", "npm install", "npm run build"]

[phases.build]
commands = ["php artisan optimize:clear"]

[start]
cmd = "php-fpm -F"