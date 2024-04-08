FROM php:8.2.8-fpm

RUN apt-get update && apt-get install -y locales libonig-dev zlib1g-dev libpng-dev libxml2-dev libzip-dev libfreetype6-dev && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    mysqli \
    pdo_mysql \
    mbstring \
    bcmath \
    soap \
    zip \
    intl

RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev && \
    docker-php-ext-configure gd --with-jpeg=/usr/include/ && \
    docker-php-ext-install gd

RUN docker-php-ext-install opcache

RUN echo "opcache.enable=1" >> /usr/local/etc/php/php.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/php.ini \
    && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/php.ini \
    && echo "opcache.max_accelerated_files=4000" >> /usr/local/etc/php/php.ini \
    && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/php.ini \
    && echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/php.ini

RUN echo "max_input_vars = 10000" >> /usr/local/etc/php/php.ini

RUN echo "mysqli.default_socket=/var/run/mysqld/mysqld.sock" >> /usr/local/etc/php/conf.d/custom.ini


CMD ["php-fpm"]