FROM php:8.3-fpm

# --- System packages ---
# libwebp/libjpeg/libpng/libfreetype => برای GD با پشتیبانی WebP (کل پروژه با ->encode('webp', ...) کار می‌کنه)
# libssl-dev + pkg-config           => برای build شدن اکستنشن mongodb
# libcurl4-openssl-dev              => برای اکستنشن curl (guzzle/elasticsearch-php ازش استفاده می‌کنن)
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    curl \
    zip \
    ffmpeg \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    libonig-dev \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

# --- PHP extensions ---
# mbstring : لاراول بدونش اصلاً بوت نمی‌شه (پیش‌نیاز composer.json خود فریم‌ورکه)
# pdo_mysql/mysqli : بخشی از مدل‌ها (Admin, Mission, CategoryFeature, SiteCategory, ...) هنوز mysql هستن
# gd   : Intervention Image (پردازش عکس با خروجی webp)
# zip  : آپلود/laravel-filemanager
# bcmath, curl, pcntl : نیازهای استاندارد لاراول + queue:work (pcntl برای graceful shutdown صف)
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
    mbstring \
    pdo_mysql \
    mysqli \
    gd \
    zip \
    bcmath \
    curl \
    pcntl

# --- اکستنشن‌های PECL ---
# redis لازم است چون کش/سشن/صف (queue-general) از همان Redis پروژه‌ی musicbot استفاده می‌کنند
RUN pecl install redis \
    && docker-php-ext-enable redis

# --- MongoDB extension: دیتابیس اصلی پروژه (mongodb/laravel-mongodb بدونش کار نمی‌کنه) ---
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# --- تنظیمات PHP (مطابق نیاز واقعی پروژه: آپلود ویدیو/عکس، ffmpeg، crawl) ---
RUN echo "upload_max_filesize = 300M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 300M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_input_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/uploads.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www

ENTRYPOINT ["entrypoint.sh"]