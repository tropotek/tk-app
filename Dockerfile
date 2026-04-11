FROM dunglas/frankenphp:latest AS base

# Set the working directory inside the container
WORKDIR /app

# The EXPOSE instruction only serves as documentation.
EXPOSE 80 443

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
    procps \
    unzip \
    vim-nox \
    git \
    openssh-client \
    gnupg \
    ca-certificates \
    curl \
    && curl -fsSL https://deb.nodesource.com/setup_24.x | bash - \
    && apt-get install -y --no-install-recommends \
    nodejs \
    && npm install -g npm@latest \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Verify node installation
RUN node -v && npm -v

# Install required PHP extensions for Moodle
RUN install-php-extensions \
    date \
    intl \
    pcntl \
    gd \
    tidy \
    sockets \
    zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ARG APP_USER=appuser
RUN \
    useradd -m ${APP_USER}; \
    setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp; \
    chown -R ${APP_USER}:${APP_USER} /config/caddy /data/caddy

RUN chown -R ${APP_USER}:${APP_USER} /app

# Running as a Non-Root User

# Setup .bashrc
RUN echo 'alias l="ls -lah --color=auto"' >> ~/.bashrc


FROM base AS release
ARG APP_USER=appuser

USER ${APP_USER}

COPY . /app
ENTRYPOINT ["./bin/deploy.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]


FROM base AS local
ARG APP_USER=appuser
USER ${APP_USER}
ENTRYPOINT ["./bin/deploy.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
