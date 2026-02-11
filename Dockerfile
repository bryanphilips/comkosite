FROM php:8.2-apache

# Enable Apache rewrite (nice for routing)
RUN a2enmod rewrite

# Copy your site
COPY . /var/www/html/

# If you have a public/ folder, change DocumentRoot (optional)
# RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf