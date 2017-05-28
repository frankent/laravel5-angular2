FROM keittirat/nds-php7:latest
COPY . /var/www/html
WORKDIR /var/www/html
VOLUME /var/www/html


RUN apk update --no-cache
RUN apk add --no-cache \
                bash \
                nodejs \
                git \
                python2

RUN cd /var/www/html
RUN npm install --only=production

RUN apk del nodejs \
            git \
            python2
        
CMD ["php-fpm7", "-F"]