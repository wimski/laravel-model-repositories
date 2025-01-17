FROM php:8.4-cli-alpine

RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    && apk add --no-cache \
        shadow \
    && pecl install \
        pcov \
    && docker-php-ext-enable \
        pcov \
    && apk del -f .build-deps \
    && rm -rf /tmp/pear

ARG PUID=1000
ENV PUID=${PUID}
ARG PGID=1000
ENV PGID=${PGID}

RUN groupmod -o -g ${PGID} nobody && \
    usermod -o -u ${PUID} -g nobody nobody && \
    apk del shadow

RUN curl -s https://getcomposer.org/installer | \
    php -- --install-dir=/usr/local/bin/ --filename=composer \
    && mkdir /.composer \
    && chown -R nobody:nobody /.composer

USER nobody

WORKDIR /var/www/html
