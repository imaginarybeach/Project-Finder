FROM mysql:latest

# Environment variables for MySQL
ENV MYSQL_ROOT_PASSWORD=rootpassword
ENV MYSQL_DATABASE=studentdb
ENV MYSQL_USER=user
ENV MYSQL_PASSWORD=password

# Copy SQL files to docker-entrypoint-initdb.d
# Files in this directory are executed in alphabetical order when the container starts
COPY 1Create.sql /docker-entrypoint-initdb.d/1-schema.sql
COPY 2Load.sql /docker-entrypoint-initdb.d/2-load.sql

# Copy CSV files to a location inside the container
COPY *.csv /var/lib/mysql-files/

# Set secure_file_priv to allow MySQL to read from this directory
RUN echo "secure_file_priv=/var/lib/mysql-files/" >> /etc/mysql/conf.d/docker.cnf