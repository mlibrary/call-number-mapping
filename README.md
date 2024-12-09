Check example.env for environment variables to set.


```
docker-compose build

# Start the webserver and database server
docker-compose up -d

# Composer install for the development volume
docker-compose run --rm web bash -c 'cd /var/www && composer install'

# Optional:  Load data
docker-compose exec -T db bash -c 'mysql -u "$MARIADB_USER" -p"$MARIADB_PASSWORD" "$MARIADB_DATABASE"' < data.sql


```
