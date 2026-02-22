FROM dunglas/frankenphp:latest

# Expose port 80 (or 443 if using HTTPS)
EXPOSE 80

# Install requred apt packages
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    libzip-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libicu-dev \
    libxml2-dev \
    unzip \
    vim-nox \
    git \
    openssh-client \
    cron \
    curl \
    wget \
    ca-certificates \
    default-mysql-client

# Install required PHP extensions for Moodle
RUN install-php-extensions \
    intl \
    zip \
    soap \
    gd \
    tidy \
    mysqli \
    pdo_mysql \
    exif \
    xsl \
    opcache \
    sockets \
    xmlrpc

# Enable PHP production settings
# RUN mv "/usr/local/etc/php/php.ini-production" "/usr/local/etc/php/php.ini"
RUN mv "/usr/local/etc/php/php.ini-development" "/usr/local/etc/php/php.ini"
RUN echo 'max_input_vars = 5000' >> "/usr/local/etc/php/conf.d/moodle.ini";

# Setup .bashrc
RUN echo 'alias l="ls -lah --color=auto"' >> ~/.bashrc

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Clean apt cache in one layer
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Create required Laravel directories for storage and caching
RUN mkdir -p storage/framework/{sessions,views,cache} \
   && mkdir -p storage/logs \
   && mkdir -p bootstrap/cache

# Set the working directory inside the container
WORKDIR /app

# 'cron -f' runs cron in the foreground, which is necessary to keep the container alive
CMD cron -f & exec frankenphp run --config /etc/caddy/Caddyfile
