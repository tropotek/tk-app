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
    curl \
    gnupg \
    ca-certificates \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

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

# Install dependencies for adding new repositories and for Node.js build tools
RUN curl -fsSL https://deb.nodesource.com
RUN apt-get update
RUN apt-get install -y nodejs npm

# Verify installation
RUN node -v
RUN npm -v

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

RUN chown -R ${USER}:${USER} /app
USER ${USER}

# Setup .bashrc
RUN echo 'alias l="ls -lah --color=auto"' >> ~/.bashrc

FROM base AS release
COPY . /app
ENTRYPOINT ["./bin/deploy.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]

FROM base AS local
ENTRYPOINT ["./bin/deploy.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
