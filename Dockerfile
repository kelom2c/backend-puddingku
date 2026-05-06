FROM php:8.2-apache
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Configure Apache to listen on $PORT provided by Railway
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html
