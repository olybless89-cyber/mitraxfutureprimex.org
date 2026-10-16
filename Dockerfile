FROM php:7.4-apache

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql mysqli gd zip bcmath gmp intl exif opcache @composer \
 && a2dismod mpm_event mpm_worker 2>/dev/null; a2enmod mpm_prefork rewrite headers

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/start.sh /usr/local/bin/start.sh

WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist

COPY . .
RUN rm -f bootstrap/cache/*.php bootstrap/cache/*.tmp \
 && composer dump-autoload --optimize --no-dev --no-scripts \
 && mkdir -p storage/framework/{cache/data,sessions,views,testing} storage/logs storage/app/public/photos bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod +x /usr/local/bin/start.sh

ENV PORT=8080
CMD ["/usr/local/bin/start.sh"]
