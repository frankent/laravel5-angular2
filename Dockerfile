FROM php:7.1.5-fpm-alpine

WORKDIR /var/www/html
RUN apk update --no-cache &&
    apk add --no-cache \
                nodejs \
                git \
                python

RUN docker-php-ext-install \
			mcrypt \
			zip \
			gettext \
			bz2 \
			gd

COPY . /var/www/html
RUN npm install && gulp --production

CMD ["sh","/usr/bin/docker-start.sh"]