# atytude-com
https://atytude.com

# Install Ubuntu App from MS Store + WSL
Get the app from: https://www.microsoft.com/en-us/p/ubuntu/9nblggh4msv6

Then run in a powershell (AS ADMINISTRATOR)
```bash
dism.exe /online /enable-feature /featurename:Microsoft-Windows-Subsystem-Linux /all /norestart
```

After this, restart your computer.

# Local Installation (WSL1 / Ubuntu App from MS Store)

Run the Ubuntu terminal (search for Ubuntu and run the app) and follow the commands below line by line:

```bash
sudo mv /bin/sleep /bin/sleep~
sudo touch /bin/sleep
sudo chmod +x /bin/sleep

sudo apt-get update
sudo apt-get upgrade
sudo add-apt-repository ppa:ondrej/php
sudo apt-get install php7.3-bcmath php7.3-cli php7.3-common php7.3 php7.3-curl php7.3-dev php7.3-fpm php7.3-gd php7.3-intl php7.3-json php7.3-mbstring php7.3-mysql php7.3-opcache php7.3-readline php7.3-soap php7.3-sqlite3 php7.3-xml php7.3-zip

sudo apt-get install -y curl git unzip mysql-server network-manager libnss3-tools jq xsel libnss3-tools jq xsel build-essential libssl-dev zip unzip zlib1g-dev libzip-dev pkg-config libmemcached-dev libmemcached11 libmemcachedutil2 libmagickwand-dev imagemagick memcached redis-server network-manager dnsmasq nginx mysql-server

sudo update-alternatives --set php /usr/bin/php7.3

sudo usermod -d /var/lib/mysql/ mysql
sudo service mysql start
sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'password'; FLUSH PRIVILEGES;"
sudo service mysql restart

curl -sL https://deb.nodesource.com/setup_13.x | sudo -E bash -
sudo apt-get install nodejs

curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer

echo 'export PATH="$PATH:$HOME/.config/composer/vendor/bin"' >> ~/.bashrc
source ~/.bashrc
composer global require valeryan/valet-wsl
valet install

sudo update-rc.d mysql defaults
sudo update-rc.d php7.3-fpm defaults
sudo update-rc.d nginx defaults
sudo update-rc.d redis-server defaults

mkdir ~/sites
cd ~/sites

git clone --recurse-submodules -j2 https://github.com/atytude/atytude-com.git
cd atytude-com
valet link
valet secure

composer install

sed -i 's/continue;/continue 2;/g' vendor/zendframework/zend-stdlib/src/ArrayObject.php

mysql -u root -p -e "CREATE DATABASE atytude_market;"
mysql -u root -p -e "CREATE USER 'atytude_market'@'%' IDENTIFIED WITH mysql_native_password BY 'y.yMWsB0L.FT';"
mysql -u root -p -e "GRANT ALL PRIVILEGES ON atytude_market.* TO atytude_market@'%';"

# ... get db snapshot !?
# ... create app/etc/env.php !!

php -f bin/magento deploy:mode:set developer
php -f bin/magento cache:enable
php -f bin/magento setup:di:compile
php -f bin/magento config:set web/unsecure/base_url http://atytude-com.test/
php -f bin/magento config:set web/secure/base_url http://atytude-com.test/
php -f bin/magento config:set web/cookie/cookie_domain atytude-com.test
```

# Windows 10 Acrylic DNS Proxy Setup
https://mayakron.altervista.org/support/acrylic/Home.htm
- Install + Set up DNS Server

# Home folder location (for windows explorer / editor of choice)
```bash
\\wsl$\Ubuntu\home
```

# How to maintain DB changes
https://www.mageplaza.com/magento-2-module-development/magento-2-how-to-create-sql-setup-script.html

# CSS/HTML changes
Sometimes you might need to run the following on local if magento doesn't automatically copy the changes over:
```bash
php -f bin/magento setup:static-content:deploy -f en_US ro_RO
```

Alternatively by using grunt, first to set it up:
```bash
sudo apt-get install -y npm
sudo npm install -g grunt-cli
npm install
npm update

cp dev/tools/grunt/configs/themes.js dev/tools/grunt/configs/local-themes.js
```

To clear pub/static and var/view_preprocessed
```bash
grunt clean
```

To compile themes (only "tukan" needed for now on frontend)
```bash
grunt exec
```

And to compile on the fly run
```bash
grunt watch
```

# Github Process
whatsapp:
```bash
ok deci ideea e cam asa:

- inainte de a face modificari/commit rulezi: git pull
- dupa care ne mutam pe un branch nou folosind comanda: git checkout -b herk_123
 (unde herk_123 e un nume pentru branch, ca sa aveti idee de ce ati facut acolo, in mare)
- odata facut asta, lucrezi ca si normal, foarte important ca la "git status" sa nu apara ca sunteti pe master ci pe branchul creat, faceti commit normal acolo
- la git push, o sa apara o comanda pe care o cere git, trebuie facut doar copy paste si rulata aia
- dupa care puteti rula: git checkout master
 si o luati de la capat

si cam atat, asta inseamna ca s-a creat un branch nou si a fost urcat in github cu noile modificari
dupa care puteti lasa un mesaj aici si le fac eu merge, pot sa fac asta oricand ca nu dureaza mult
```

```bash
git pull
git checkout -b herk_123_new_branch_checkout_changes
... make your changes
git commit
git push (copy/paste git command since git push won't work straight away on your _first_ commit, commits after this will work normally)
... once you are done with all the commits switch to master and repeat this process
git checkout master
```

# Master push

```bash
git checkout master
git fetch -a
git pull
git cherry-pick <commit-sha>
.. fix conflicts
git push
```
