FROM nginx:alpine as production

COPY ./docker/nginx-proxy/*.conf /etc/nginx/
COPY ./docker/nginx-proxy/conf.d /etc/nginx/conf.d

ENV HTML_ROOT /var/www/html
RUN rm -rf ${HTML_ROOT}/*
COPY www ${HTML_ROOT}/www
WORKDIR ${HTML_ROOT}

FROM production as debug

RUN mv /etc/nginx/debug.conf /etc/nginx/conf.d/debug.conf \
  && echo 'include php_debug.conf;' >> /etc/nginx/php.conf
