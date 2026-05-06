FROM php:8.2-cli
RUN docker-php-ext-install mysqli pdo pdo_mysql
COPY . /var/www/html/
WORKDIR /var/www/html
# Menjalankan PHP built-in server pada port yang diberikan Railway
CMD ["sh", "-c", "php -S 0.0.0.0:$PORT"]
