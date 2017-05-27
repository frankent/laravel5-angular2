FROM keittirat/nds-php7:latest

WORKDIR /var/www/html
RUN apk update --no-cache &&
    apk add --no-cache \
                nodejs \
                git \
                python2

COPY . /var/www/html
RUN npm install && gulp --production
CMD ["sh","/usr/bin/docker-start.sh"]