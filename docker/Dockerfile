FROM dunglas/frankenphp:latest

# Set the working directory inside the container
WORKDIR /app

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
    curl \
    gnupg \
    ca-certificates

# Install required PHP extensions for Moodle
RUN install-php-extensions \
    curl \
    date \
    intl \
    json \
    mbstring \
    pcntl \
    pdo_sqlite \
    gd \
    tidy \
    sockets \
    zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Clean apt cache in one layer
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Create required Laravel directories for storage and caching
RUN mkdir -p storage/framework/{sessions,views,cache} \
   && mkdir -p storage/logs \
   && mkdir -p bootstrap/cache


# Running as a Non-Root User
ARG USER=appuser
RUN \
    useradd -m ${USER}; \
    setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp; \
    chown -R ${USER}:${USER} /config/caddy /data/caddy

COPY . /app
RUN chown -R ${USER}:${USER} /app

USER ${USER}

# Setup .bashrc
RUN echo 'alias l="ls -lah --color=auto"' >> ~/.bashrc

RUN composer install --no-dev --no-interaction --prefer-dist


# TODO: look for other ways to optimize the image size
# - remove composer
# - uninstall (purge) git, vim, openssh-client, etc

# Set the entrypoint
ENTRYPOINT ["./bin/deploy.sh"]

# Default command to run if none is provided
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
