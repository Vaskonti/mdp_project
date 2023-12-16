# Use a smaller base image
ARG PHP_VERSION=8.2
FROM php:${PHP_VERSION}-fpm-alpine

# Define environment variables
ARG WORKDIR=/var/www/html
ENV DOCUMENT_ROOT=${WORKDIR} \
    LARAVEL_PROCS_NUMBER=1 \
    NODE_MAJOR=20 \
    HOST_UID=1000 \
    USER=www-data

# Install system dependencies
RUN apk add --no-cache \
        curl \
        freetype-dev \
        libjpeg-turbo-dev \
        libmemcached-dev \
        supervisor \
        sqlite \
        dcron \
        build-base \
        autoconf \
        pkgconfig \
        libc-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql \
    && pecl install redis mongodb \
    && docker-php-ext-enable redis mongodb \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /var/cache/apk/*

# Set working directory
WORKDIR $WORKDIR

# Copy necessary files
COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# Check if the group exists before adding it
RUN if ! getent group ${USER} > /dev/null 2>&1; then \
        addgroup -g ${HOST_UID} ${USER}; \
    fi

# Add user to the group
RUN if ! getent passwd ${USER} > /dev/null 2>&1; then \
        adduser -u ${HOST_UID} -G ${USER} -s /bin/sh -D ${USER}; \
    fi

# Set permissions
RUN chmod -R 755 $WORKDIR \
    && chown -R www-data:www-data $WORKDIR

# Expose port
EXPOSE 9000

# Entrypoint
ENTRYPOINT ["entrypoint.sh"]

# Healthcheck (optional)
HEALTHCHECK CMD curl --fail http://localhost || exit 1

# Default command
CMD ["entrypoint"]
