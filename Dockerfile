FROM keittirat/nds-php7:latest
RUN mkdir /web
RUN mkdir /web/maengron

COPY setup/crond.txt /var/spool/cron/crontabs/root
COPY . /web/maengron
RUN rm -rf /web/maengron/setup

WORKDIR /web/maengron

RUN apk update --no-cache
RUN apk upgrade
RUN apk add --no-cache \
                php7 \
                php7-zlib \
                php7-gd \
                bash \
                nodejs \
                git \
                python2

RUN cd /web/maengron
RUN npm install gulp -g && npm install && gulp --production
RUN apk del nodejs \
            git \
            python2

VOLUME /web/maengron
        
CMD ["php-fpm7", "-F"]