# Dockerfile cho CARES Web - chuẩn AMD64 trên WSL2
FROM php:8.2-apache

# Cập nhật, cài các gói cần thiết
RUN apt-get update && apt-get install -y \
        libzip-dev \
        unzip \
        libonig-dev \
        libxml2-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        && docker-php-ext-configure gd --with-freetype --with-jpeg \
        && docker-php-ext-install mysqli pdo pdo_mysql gd \
        && a2enmod rewrite \
        && apt-get clean && rm -rf /var/lib/apt/lists/*

# === THÊM CẤU HÌNH OUTPUT BUFFERING ĐỂ KHẮC PHỤC LỖI CHUYỂN HƯỚNG Headers Already Sent ===
RUN echo "output_buffering = On" >> /usr/local/etc/php/conf.d/custom-config.ini
# ========================================================================================

# Copy toàn bộ source code CARES vào container
COPY . /var/www/html/

# Chỉ định quyền nếu cần (tùy dự án)
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 cho Apache
EXPOSE 80

# Command mặc định
CMD ["apache2-foreground"]