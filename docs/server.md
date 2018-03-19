# Example SlabPHP Web Server Configuration

Depending on where your projects public folder is, you could point an apache vhost to that directory with something similar to this:

    <VirtualHost *:80>
        DocumentRoot ~/myproject/public
        ServerName local.slabproject.com
    
        <Directory "~/myproject/public">
            Options All +MultiViews
            AllowOverride None
            Order allow,deny
            Allow from all
            Require all granted
    
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteCond %{REQUEST_URI} !^/server-status
            RewriteRule ^(.*)$ index.php/$1 [L]
        </Directory>
    </VirtualHost>

or with nginx something like this

    server {
        listen 80;
        listen 443 ssl;
        server_name local.slabproject.com;
        rewrite ^(.*)$ /index.php/$1 last;
    }