# Copyright {2018}
# Pour créer l'image docker build . -t http
# Image de base
#FROM debian:stretch
#

#EXPOSE 80


# Copyright {2018}
# Pour créer l'image docker build . -t http
# Image de base
FROM debian:stretch
#Installer les packages nécessaires
RUN apt-get update && \
    apt-get -y install apache2 && \
    apt-get -y install php && \
    apt-get -y install  php-pear && \
    apt-get -y install  php-mysql && \
    #apt-get -y install php7.0-mysqli && \
    rm -rf /var/www/hmtl/index.html


#RUN cp index.html http:/index.html
FROM php:7.3-apache
RUN docker-php-ext-install mysqli

COPY . /var/www/html/
RUN chmod 777 -R /var/www/html/
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
EXPOSE 8080
CMD /usr/sbin/apachectl -D FOREGROUND
#RUN apache2 -D FOREGROUND
