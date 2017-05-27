# FROM php:7.1.5-fpm-alpine
FROM keittirat/nds-php7:latest
WORKDIR /var/www/html
RUN apk update --no-cache && apk add --no-cache \
                nodejs \
                git \
                python2
# RUN apk update --no-cache && apk add --no-cache \
#                 nodejs \
#                 git \
#                 python

# RUN docker-php-ext-install \
# 			mcrypt \
# 			zip \
# 			gettext \
# 			bz2 \
# 			gd

COPY . /var/www/html
RUN npm update
RUN npm install gulp -g
RUN gulp --production

CMD ["sh","/usr/bin/docker-start.sh"]