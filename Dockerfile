FROM php:8.2-apache

# Enable Apache rewrite (nice for routing)
RUN a2enmod rewrite

# System deps + common PHP extensions (adjust as needed)
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip zip curl \
    libzip-dev \
  && docker-php-ext-install zip \
  && rm -rf /var/lib/apt/lists/*

# --- Install Composer (official method: copy from composer image) ---
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# App code
WORKDIR /var/www/html
COPY . /var/www/html/

# (Optional) install PHP deps if you have composer.json
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader

# (Optional) If you have a public/ folder, change DocumentRoot
# RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf
# RUN sed -i 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf

# Permissions (optional, depends on your app)
# RUN chown -R www-data:www-data /var/www/html