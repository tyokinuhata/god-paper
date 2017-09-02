# /bin/sh

sh ~/vagrant_config/initfile/init.sh

rm /etc/apache2/sites-available/000-default.conf
cp ~/vagrant_config/000-default.conf /etc/apache2/sites-available/000-default.conf

chmod 777 -R ~/html

service apache2 restart

cd ~/html/
composer install
cp .env.example .env
php artisan key:generate
npm install


