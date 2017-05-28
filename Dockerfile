FROM keittirat/nds-php7:latest
RUN mkdir /web
RUN mkdir /web/maengron
COPY . /web/maengron
WORKDIR /web/maengron
VOLUME /web/maengron


RUN apk update --no-cache
RUN apk add --no-cache \
                bash \
                nodejs \
                git \
                python2

RUN cd /web/maengron
RUN npm install --only=production

RUN apk del nodejs \
            git \
            python2
        
CMD ["php-fpm7", "-F"]