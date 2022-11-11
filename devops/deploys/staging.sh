#!/usr/bin/env bash

# Define a timestamp function
timestamp() {
  date +"%Y%m%d%H%M%S"
}

deploy_timestamp() {
  date +"%Y-%m-%d %H:%M:%S"
}

docroot=/var/www/cleo.syngenta.dev.theformery.com/htdocs/
backup_dir=/home/formery/backups/cleo/
database_backup=${backup_dir}mysqldump$(timestamp).sql

echo "Step 1: Enable NGINX Maintenance Mode"
touch /home/formery/flags/cleo-maintenance.on
echo "Step 2: Run backups"
echo "Step 2.1: Backing up database"

mysqldump -u${DB_USER} -p${DB_PASSWORD} --databases ${DB_DATABASE} > ${database_backup}

echo "Step 3: Starting deploy process"
echo "Step 4: Pulling latest files from repo..."

git -C ${docroot} fetch --all
git -C ${docroot} checkout --force "origin/deploy/staging"

echo "Step 5: Changing to document root..."

cd ${docroot} || exit

echo "Step 6: Set Permissions"

sudo find ${docroot} -type f -exec chmod 644 {} \;
sudo find ${docroot} -type d -exec chmod 755 {} \;
sudo chgrp -R www-data ${docroot}storage ${docroot}bootstrap/cache
sudo chmod -R ug+rwx ${docroot}storage ${docroot}bootstrap/cache

echo "Step 7: Enable Larevel Maintenance Mode"
php artisan down --message="Performing System Updates. Please be patient." --retry=60

echo "Step 8: Cleanup"

php artisan config:clear
php artisan cache:clear
php artisan view:clear
rm -rf node_modules/*

echo "Step 9: Running Installs"

composer update --optimize-autoloader --no-dev --no-ansi

npm install
npm run production

php artisan migrate
php artisan config:cache

echo "Step 10: Deploy Completed"
echo "Step 11: Disable Maintenance Mode"

php artisan up
rm -rf /home/formery/flags/cleo-maintenance.on
