#!/bin/sh
echo "##########################################################"
echo "Initialize Program on UNILORN!!"
echo "##########################################################"
echo ""
echo "####################### apt update #######################"
sudo apt update
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt -y install curl wget
sudo apt -y install vim

sudo apt -y install php7.1 php7.1-mysql php7.1-dev php7.1-fpm php7.1-mbstring php7.1-cli
#sudo apt -y install mysql-server mysql-client
sudo apt -y install apache2
sudo apt -y install ruby
sudo apt -y install git
sudo apt -y install libapache2-mod-php7.0
sudo apt -y install npm
sudo npm -g install n
sudo n latest

#sudo curl -o /usr/local/bin/jq -L https://github.com/stedolan/jq/releases/download/jq-1.5/jq-linux64 && sudo chmod +x /usr/local/bin/jq

# sudo apt -y install fish
# curl -Lo ~/.config/fish/functions/fisher.fish --create-dirs git.io/fisher

echo "################### composer install #####################"
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer config -g repositories.packagist composer https://packagist.jp

# 任意
# sudo apt -y upgrade


