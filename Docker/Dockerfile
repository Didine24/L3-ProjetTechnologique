FROM ubuntu:14.04

MAINTAINER Jean-François GAZET <theboss@jeffprod.com>

LABEL version="1.0"
LABEL description="Apache 2 / PHP"

RUN apt-get -y update && apt-get install -y \
apache2 \
apache2-utils \
php5 \
libapache2-mod-php5 \
php5-gd \
php5-json \
php5-sqlite \
php5-mysql \
php5-mcrypt \
mcrypt \
mysql-server \
mysql-client


# on veut une machine de dev qui affiche toutes les erreurs PHP
RUN sed -i -e 's/^error_reporting\s*=.*/error_reporting = E_ALL/' /etc/php5/apache2/php.ini
RUN sed -i -e 's/^display_errors\s*=.*/display_errors = On/' /etc/php5/apache2/php.ini

# commandes à exécuter au démarrage de l'instance de l'image
# ici on démarrera Apache

ENTRYPOINT service mysql start && service apache2 start && bash
