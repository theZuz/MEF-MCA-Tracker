FROM php:7.3-fpm-alpine as production

RUN apk add --no-cache \
	#composer
	git \
	&& docker-php-ext-install \
	    opcache \
	    pdo \
	    pdo_mysql \
	&& rm -rf /tmp/pear

COPY ./docker/application/cmd-run /usr/local/bin/cmd-run
COPY ./docker/application/log-run /usr/local/bin/log-run
COPY ./docker/application/php/conf.d /usr/local/etc/php/conf.d

ENV HTML_ROOT /var/www/html
RUN rm -rf ${HTML_ROOT}/*

COPY app ${HTML_ROOT}/app
COPY log ${HTML_ROOT}/log
COPY phinx.php ${HTML_ROOT}/

RUN chmod 777 \
	${HTML_ROOT} \
	${HTML_ROOT}/log

COPY composer.json composer.lock ${HTML_ROOT}/
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_DISCARD_CHANGES 1
ENV COMPOSER_HOME /tmp/composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR ${HTML_ROOT}
RUN composer install --optimize-autoloader --classmap-authoritative

USER www-data
CMD ["cmd-run"]

FROM production as development

USER root

RUN rm /usr/local/etc/php/conf.d/production.ini

USER www-data

FROM development as debug

USER root

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
	&& pecl install \
		xdebug \
	&& docker-php-ext-enable \
		xdebug \
	&& apk del .build-deps

USER www-data
