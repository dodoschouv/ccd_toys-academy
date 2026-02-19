# Stage 1: build frontend Vue + Tailwind
FROM node:20-alpine AS frontend-build
WORKDIR /app/frontend

COPY frontend/package.json frontend/package-lock.json* ./
RUN npm ci 2>/dev/null || npm install

COPY frontend/ ./
RUN npm run build

# Stage 2: PHP Apache + backend + frontend static
FROM php:8.2-apache
WORKDIR /var/www/html

# Document root = backend/public, enable rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/backend/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite headers

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Backend PHP Slim
COPY backend/ ./backend/
RUN cd backend && composer install --no-dev --optimize-autoloader

# SPA Vue (index.html + assets) dans public/
COPY --from=frontend-build /app/frontend/dist/ ./backend/public/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
