# Copyright {2018} {Viardot Sebastien}
# Pour créer l'image docker build . -t bd
# Image de base
FROM debian:stable
# Auteur
MAINTAINER Sebastien Viardot <Sebastien.Viardot@grenoble-inp.fr>
# Installe les packages du serveur web
RUN apt-get update && \
    apt-get -y install mariadb-server mariadb-client && \
    rm -rf /var/lib/apt/lists/*
WORKDIR /tmp
COPY DB.sql .
# Modifie la configuration pour permettre l'accès depuis un autre
RUN	find /etc/mysql/ -name '*.cnf' -print0 \
		| xargs -0 grep -lZE '^(bind-address|log)' \
		| xargs -rt -0 sed -Ei 's/^(bind-address|log)/#&/'; \
	  echo '[mysqld]\nskip-host-cache\nskip-name-resolve' > /etc/mysql/conf.d/docker.cnf
RUN service mysql start && mysql < DB.sql && service mysql stop && rm /tmp/DB.sql
# Version avec un serveur mariadb, lancer le container avec docker run -d -p 3306:3306 --rm name db1 bd
EXPOSE 3306
CMD ["/usr/sbin/mysqld"]
