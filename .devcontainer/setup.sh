#!/bin/bash
set -e

echo "=== Installing phpMyAdmin ==="

# Install phpMyAdmin
sudo apt-get update -q
sudo apt-get install -y phpmyadmin php-mbstring php-zip php-gd php-json php-curl

# Konfigurasi phpMyAdmin agar bisa diakses tanpa password root
sudo bash -c 'cat > /etc/phpmyadmin/config-db.php <<EOF
<?php
\$dbuser="phpmyadmin";
\$dbpass="";
\$basepath="";
\$dbname="phpmyadmin";
\$dbserver="localhost";
\$dbport="3306";
\$dbtype="mysql";
EOF'

# Buat symlink agar phpMyAdmin bisa diakses via Apache
sudo ln -sf /usr/share/phpmyadmin /var/www/html/phpmyadmin

# Aktifkan modul Apache yang diperlukan
sudo a2enmod mbstring rewrite
sudo service apache2 restart

# Atur Apache listen di port 8080
sudo sed -i 's/Listen 80$/Listen 8080/' /etc/apache2/ports.conf
sudo sed -i 's/<VirtualHost \*:80>/<VirtualHost \*:8080>/' /etc/apache2/sites-enabled/000-default.conf

# Buat virtual host phpMyAdmin di port 8081
sudo bash -c 'cat > /etc/apache2/sites-available/phpmyadmin.conf <<EOF
Listen 8081
<VirtualHost *:8081>
    DocumentRoot /usr/share/phpmyadmin
    <Directory /usr/share/phpmyadmin>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOF'

sudo a2ensite phpmyadmin.conf
sudo service apache2 restart

# Start MySQL
sudo service mysql start

# Set MySQL root tanpa password (untuk dev)
sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY ''; FLUSH PRIVILEGES;" 2>/dev/null || true

echo "=== Setup selesai! ==="
echo "phpMyAdmin tersedia di port 8081"