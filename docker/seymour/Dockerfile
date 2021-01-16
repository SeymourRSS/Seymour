FROM php:8.0-fpm-alpine

LABEL maintainer="Ryan Durham <ryan@stagerightlabs.com>"

# set up www-data user as UID 1000
ARG NAME=www-data
ENV NAME ${NAME}
RUN deluser www-data && \
    adduser -s /bin/sh -D -u 1000 -g '' ${NAME} ${NAME} && \
    chown -R ${NAME}:${NAME} /home/${NAME}

# add necessary apk packages
RUN apk add --no-cache autoconf g++ libtool make supervisor

# install required php extensions
RUN docker-php-ext-install bcmath \
    opcache \
    pdo

# configure php
COPY php-local.ini /usr/local/etc/php/conf.d/
COPY xlaravel.pool.conf /usr/local/etc/php-fpm.d/

# make sure that ~/.composer belongs to user
RUN mkdir /home/${NAME}/.composer && chown -R ${NAME}:${NAME} /home/${NAME}

# composer
COPY ./install-composer.php /home/${NAME}/
RUN php /home/${NAME}/install-composer.php --install-dir=/usr/local/bin --filename=composer && \
    rm /home/${NAME}/install-composer.php

# configure supervisor
COPY supervisord.conf /etc/supervisord.conf
RUN mkdir -p /var/log/supervisor/ && touch /var/log/supervisor/supervisord.log
RUN touch /var/run/supervisord.pid

# configure crontab
COPY crontab.tmp /home/${NAME}/crontab.tmp
RUN crontab /home/${NAME}/crontab.tmp && \
    rm /home/${NAME}/crontab.tmp

# clean
RUN apk del autoconf g++ libtool make
RUN rm -rf /tmp/* /var/cache/apk/*

WORKDIR /var/www
USER ${NAME}
ENTRYPOINT ["docker-php-entrypoint"]
