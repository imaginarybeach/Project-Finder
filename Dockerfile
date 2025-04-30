FROM mysql:latest

# Environment variables for MySQL
ENV MYSQL_ROOT_PASSWORD=rooty
ENV MYSQL_DATABASE=Project-Finder
ENV MYSQL_USER=root
ENV MYSQL_PASSWORD=rooty

# Create directory for CSV files
RUN mkdir -p /var/lib/mysql-files/ && chmod 777 /var/lib/mysql-files/

# Copy SQL files to docker-entrypoint-initdb.d
# Files in this directory are executed in alphabetical order when the container starts
COPY 1Create.sql /docker-entrypoint-initdb.d/1-schema.sql
COPY 2Load.sql /docker-entrypoint-initdb.d/2-load.sql

# Copy CSV files to the secure file location
COPY *.csv /var/lib/mysql-files/

# Set permissions for the CSV files
RUN chmod 644 /var/lib/mysql-files/*.csv

# Configure MySQL to allow reading from this directory
RUN echo "[mysqld]\nsecure_file_priv=/var/lib/mysql-files/\nlocal_infile=1" > /etc/mysql/conf.d/docker.cnf

# Expose MySQL port
EXPOSE 3306