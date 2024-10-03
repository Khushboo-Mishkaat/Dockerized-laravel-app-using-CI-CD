# Use the official PHP image with Apache
FROM php:8.1-apache 

# Set the working directory inside the container
WORKDIR /var/www/html

# Install necessary PHP extensions
RUN apt-get update && \
    apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev && \
    docker-php-ext-install pdo pdo_mysql mysqli && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

# Enable Apache mod_rewrite for Laravel routing
RUN a2enmod rewrite

# Copy the application files to the container
COPY . .

# Set the correct Apache configuration file
COPY apache.conf /etc/apache2/sites-available/000-default.conf
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.38.0/install.sh | bash && \
    export NVM_DIR="$HOME/.nvm" && \
    . "$NVM_DIR/nvm.sh" && \
    nvm install 18.18.0 && \
    nvm use 18.18.0 && \
    npm install && \
    npm run build

RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html/storage && \
    chmod -R 775 /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage/logs

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
