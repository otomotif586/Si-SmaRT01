#!/bin/bash
set -e

echo "=== Installing MySQL ==="
sudo apt-get update -q
sudo DEBIAN_FRONTEND=noninteractive apt-get install -y \
  mysql-server \
  phpmyadmin \
  php-mbstring \
  php-zip \
  php-gd \
  php-json \
  php-curl

echo "=== Configuring MySQL ==="
sudo service mysql start

# Set root tanpa password
sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY ''; FLUSH PRIVILEGES;" 2>/dev/null || true

echo "=== Configuring Apache ports ==="
# Port 8080 untuk app utama
sudo sed -i 's/Listen 80$/Listen 8080/' /etc/apache2/ports.conf
sudo sed -i 's/<VirtualHost \*:80>/<VirtualHost \*:8080>/' /etc/apache2/sites-enabled/000-default.conf

# Port 8081 untuk phpMyAdmin
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
sudo ln -sf /usr/share/phpmyadmin /var/www/html/phpmyadmin
sudo service apache2 restart

echo "=== Done! phpMyAdmin running on port 8081 ==="