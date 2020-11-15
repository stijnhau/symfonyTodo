# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = '2'

@script = <<SCRIPT
# Install dependencies
apt-get update
apt-get install -y apache2 git curl mariadb-server mariadb-client php php-mysql php-bcmath php-bz2 php-cli php-curl php-intl php-json php-mbstring php-opcache php-soap php-sqlite3 php-xml php-xsl php-zip libapache2-mod-php npm gulp php-gd
# Configure mysql paswd
if [ ! -z `ls /var/www/vagrant/*.sql` ]; then
	# Set variable to the basename of the file, minus '.sql'
	DBNAME=`ls /var/www/vagrant/*.sql | cut -d '/' -f 5 | sed s/.sql//`

	# Create a database with that name
	sudo mysqladmin -u root create $DBNAME

	# Import the SQL into new database
	sudo mysql -u root -ppass $DBNAME < /var/www/vagrant/$DBNAME.sql

	# Create a new user with same name as new db, with password 'pass'
	sudo mysql -u root -e "CREATE USER '$DBNAME'@'%' IDENTIFIED BY 'pass';"
	sudo mysql -u root -e "GRANT ALL PRIVILEGES ON $DBNAME.* TO '$DBNAME'@'%';IDENTIFIED BY 'pass' WITH GRANT OPTION;FLUSH PRIVILEGES;"
fi
cd /var/www
echo "Y Y N N Y N Y Y N" | php bin/console doctrine:migrations:migrate

# Configure Apache
echo '<VirtualHost *:80>
	DocumentRoot /var/www/public
	AllowEncodedSlashes On
	<Directory /var/www/public>
		Options +Indexes +FollowSymLinks
		DirectoryIndex index.php index.html
		Order allow,deny
		Allow from all
		AllowOverride All
	</Directory>
	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf
a2enmod rewrite
a2enmod expires
service apache2 restart
if [ -e /usr/local/bin/composer ]; then
    /usr/local/bin/composer self-update
else
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi
# Reset home directory of vagrant user
if ! grep -q "cd /var/www" /home/vagrant/.profile; then
    echo "cd /var/www" >> /home/vagrant/.profile
fi
echo "** [ZF] Run the following command to install dependencies, if you have not already:"
echo "    vagrant ssh -c 'composer install'"
echo "** [ZF] Visit http://localhost:8080 in your browser for to view the application **"
SCRIPT

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "generic/ubuntu1910"
  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.synced_folder '.', '/var/www'
  config.vm.provision 'shell', inline: @script

  config.vm.provider "virtualbox" do |vb|
    vb.customize ["modifyvm", :id, "--memory", "1024"]
    vb.customize ["modifyvm", :id, "--name", "SymfonyAssetManager - Ubuntu 19.10"]
  end
end