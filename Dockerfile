FROM keittirat/nds-php7:debian-mongo

RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data

RUN cd /tmp && curl -sL https://deb.nodesource.com/setup_6.x | bash -

WORKDIR /var/www/html
VOLUME /var/www/html
COPY . /var/www/html


RUN apt-get update && apt-get upgrade -y
RUN apt-get install -y  git \
                        nodejs \
                        python 
RUN pwd && ls -lah
RUN npm update
RUN npm install gulp -g
RUN gulp --production

CMD ["php-fpm","-F"]