# /bin/sh

sh /home/vagrant/vagrant_config/initfile/init.sh

sudo rm /etc/apache2/sites-available/000-default.conf
sudo cp /home/vagrant/vagrant_config/000-default.conf /etc/apache2/sites-available/000-default.conf

sudo chmod 777 -R /var/www/html

sudo service apache2 restart


