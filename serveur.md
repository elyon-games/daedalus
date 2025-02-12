# Guide de Configuration d'un Serveur Nginx sur Ubtuntu

# Table des Matières

- [Guide de Configuration d'un Serveur Nginx sur Ubuntu](#guide-de-configuration-dun-serveur-nginx-sur-ubuntu)
  - [Étape 1: Installation de Nginx](#étape-1-installation-de-nginx)
    - [Sur Debian/Ubuntu](#sur-debianubuntu)
    - [Sur CentOS/RHEL](#sur-centosrhel)
  - [Étape 2: Démarrer et activer Nginx](#étape-2-démarrer-et-activer-nginx)
  - [Étape 3: Configurer le pare-feu](#étape-3-configurer-le-pare-feu)
    - [Sur Debian/Ubuntu avec UFW](#sur-debianubuntu-avec-ufw)
    - [Sur CentOS/RHEL avec firewalld](#sur-centosrhel-avec-firewalld)
  - [Étape 4: Configuration de base de Nginx](#étape-4-configuration-de-base-de-nginx)
  - [Étape 5: Déployer votre site web](#étape-5-déployer-votre-site-web)
- [Guide de Configuration de FastCGI avec Nginx](#guide-de-configuration-de-fastcgi-avec-nginx)
  - [Étape 1: Installation des Paquets Nécessaires si PHP n'est pas installé sur votre serveur](#étape-1-installation-des-paquets-nécessaires-si-php-nest-pas-installé-sur-votre-serveur)
    - [Sur Debian/Ubuntu](#sur-debianubuntu-1)
    - [Sur CentOS/RHEL](#sur-centosrhel-1)
  - [Étape 2: Configurer PHP-FPM](#étape-2-configurer-php-fpm)
  - [Étape 3: Configurer Nginx pour Utiliser FastCGI](#étape-3-configurer-nginx-pour-utiliser-fastcgi)

## Étape 1: Installation de Nginx

### Sur Debian/Ubuntu
1. **Mettre à jour le système:**
```console
   sudo apt update
```
```console
   sudo apt upgrade
```
2. **Installer Nginx**

```console 
    sudo apt install nginx
```
3. **Vérifierl'installation:**
```console
    nginx -v
```
### Sur CentOS/RHEL
1. **Mettre à jour le système:**
```console
    sudo yum update
```
2. **Installer Nginx**

```console 
    sudo yum install epel-release
```
```console 
    sudo yum install nginx
```
3. **Vérifierl'installation:**
```console
    nginx -v
```

## Étape 2: Démarrer et activer Nginx
1. **Démarrer Nginx:**
```console 
    sudo systemctl start nginx
```
2. **Activer Nginx au démarrage**
```console 
    sudo systemctl enable nginx
```

## Étape 3: Configurer le pare-feu
### Sur Debian/Ubuntu avec UFW
1. **Autoriser le trafic HTTP et HTTPS:**
```console
    sudo ufw allow 'Nginx Full'
```
###   Sur CentOS/RHEL avec firewalld
1. **Autoriser le trafic HTTP et HTTPS:**
```console
    sudo firewall-cmd --permanent --zone=public --add-service=http
```
```console
    sudo firewall-cmd --permanent --zone=public --add-service=https
```
```console
    sudo firewall-cmd --reload
```
## Étape 4: Configuration de base de Nginx
1.**Fichiers de configuration:**

Le fichier principal de configuration est situé à **/etc/nginx/nginx.conf**.
Cependant, il est souvent plus pratique de gérer les configurations de sites individuels
dans le répertoire **/etc/nginx/sites-available/** et de créer des liens symboliques vers **/etc/nginx/sites-enabled/**.


2. **Créer un fichier de configuration pour votre site:**

```console
    sudo nano /etc/nginx/sites-available/mon_site
```

3. **Exemple de configuration de base:**
```bash
    server {
        listen 80;
        server_name mon_site.com www.mon_site.com;

        root /var/www/mon_site;
        index index.html index.htm index.nginx-debian.html;

        location / {
            try_files $uri $uri/ =404;
        }
    }
```
4. **Activer la configuration:**
```console
    sudo ln -s /etc/nginx/sites-available/mon_site /etc/nginx/sites-enabled/
```
5. **Vérifier la configuration:**
```console
    sudo nginx -t
```
6. **Redémarrer Nginx:**
```console
    sudo systemctl reload nginx
```


## Étape 5: Déployer votre site web 

1. **Créer le répertoire de votre site:** 
```console
    sudo mkdir -p /var/www/mon_site
```
2. **Définir les permissions:**
```console
    sudo chown -R www-data:www-data /var/www/mon_site
```
```console
    sudo chmod -R 755 /var/www/mon_site
```
3. **Placer votre contenu dans le répertoire:**
```php
    echo "<html><h1>Bienvenue sur mon site</h1></html>" > /var/www/mon_site/index.html
```


# Guide de Configuration de FastCGI avec Nginx

## Étape 1: Installation des Paquets Nécessaires si PHP n'est pas installer sur votre serveur

### Sur Debian/Ubuntu

1. **Mettre à jour le système:**
```console
   sudo apt update
```
```console
   sudo apt upgrade
```
2. **Installer PHP-FPM**

```console 
    sudo apt install php-fpm
```

### Sur CentOS/RHEL

1. **Mettre à jour le système:**
```console
   sudo yum update
```
2. **Installer PHP-FPM**

```console 
    sudo yum install php-fpm
```


# Étape 2: Configurer PHP-FPM
1. **Modifier le fichier de configuration de PHP-FPM:**
Le fichier de configuration est généralement situé à /etc/php/8.1/fpm/php-fpm.conf (sur Debian/Ubuntu) ou /etc/php-fpm.d/www.conf (sur CentOS/RHEL).  
Assurez-vous que listen est configuré pour utiliser un socket Unix ou un port TCP. Par exemple:

```ini
    listen = /run/php/php8.1-fpm.sock
```

Soyez conscient que Sesi est pour PHP8.1. Le nom peut différer en fonction de la version PHP que vous utilisez.

2. **Démarrer et activer PHP-FPM:**
```console
    sudo systemctl start php8.1-fpm
```
```console
    sudo systemctl enable php8.1-fpm
```

# Étape 3: Configurer Nginx pour Utiliser FastCGI

1. **Créer un fichier de configuration pour votre site:** 

```console
    sudo nano /etc/nginx/sites-available/mon_site
```

2. **Ajouter la configuration FastCGI:**
Voici un exemple de configuration pour le serveur:
Dans les dossiers de votre site, créez un dossier api qui contiendra le code à appeler.

```ini
    server {
        listen 80;
        server_name www.mon_site.com;

        root /var/www/mon_site;
        index index.php index.html index.htm;

        location / {
            try_files $uri $uri/ =404;
        }

        location /api/ {
            try_files $uri $uri/ /index.php?$query_string;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            fastcgi_index index.cgi;
            include fastcgi_params;
            fastcgi_pass unix:/var/run/fcgiwrap.socket;
        }

        location ~ \.php$ {
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/run/php/php8.1-fpm.sock;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include fastcgi_params;
        }

        location ~ /\.ht {
            deny all;
        }
    }

```
3. **Activer la configuration:**
```console
    sudo ln -s /etc/nginx/sites-available/mon_site /etc/nginx/sites-enabled/
```

4. **Vérifier la configuration de Nginx:**
```console
    sudo nginx -t
```

5. **Redémarrer Nginx:**
```console
    sudo systemctl reload nginx
```


# Guide de Configuration d'un Serveur Nginx sur Ubtuntu

# Table des Matières

- [Guide de Configuration d'un Serveur Nginx sur Ubuntu](#guide-de-configuration-dun-serveur-nginx-sur-ubuntu)
  - [Étape 1: Installation de Nginx](#étape-1-installation-de-nginx)
    - [Sur Debian/Ubuntu](#sur-debianubuntu)
    - [Sur CentOS/RHEL](#sur-centosrhel)
  - [Étape 2: Démarrer et activer Nginx](#étape-2-démarrer-et-activer-nginx)
  - [Étape 3: Configurer le pare-feu](#étape-3-configurer-le-pare-feu)
    - [Sur Debian/Ubuntu avec UFW](#sur-debianubuntu-avec-ufw)
    - [Sur CentOS/RHEL avec firewalld](#sur-centosrhel-avec-firewalld)
  - [Étape 4: Configuration de base de Nginx](#étape-4-configuration-de-base-de-nginx)
  - [Étape 5: Déployer votre site web](#étape-5-déployer-votre-site-web)
- [Guide de Configuration de FastCGI avec Nginx](#guide-de-configuration-de-fastcgi-avec-nginx)
  - [Étape 1: Installation des Paquets Nécessaires si PHP n'est pas installé sur votre serveur](#étape-1-installation-des-paquets-nécessaires-si-php-nest-pas-installé-sur-votre-serveur)
    - [Sur Debian/Ubuntu](#sur-debianubuntu-1)
    - [Sur CentOS/RHEL](#sur-centosrhel-1)
  - [Étape 2: Configurer PHP-FPM](#étape-2-configurer-php-fpm)
  - [Étape 3: Configurer Nginx pour Utiliser FastCGI](#étape-3-configurer-nginx-pour-utiliser-fastcgi)

## Étape 1: Installation de Nginx

### Sur Debian/Ubuntu
1. **Mettre à jour le système:**
```console
   sudo apt update
```
```console
   sudo apt upgrade
```
2. **Installer Nginx**

```console 
    sudo apt install nginx
```
3. **Vérifierl'installation:**
```console
    nginx -v
```
### Sur CentOS/RHEL
1. **Mettre à jour le système:**
```console
    sudo yum update
```
2. **Installer Nginx**

```console 
    sudo yum install epel-release
```
```console 
    sudo yum install nginx
```
3. **Vérifierl'installation:**
```console
    nginx -v
```

## Étape 2: Démarrer et activer Nginx
1. **Démarrer Nginx:**
```console 
    sudo systemctl start nginx
```
2. **Activer Nginx au démarrage**
```console 
    sudo systemctl enable nginx
```

## Étape 3: Configurer le pare-feu
### Sur Debian/Ubuntu avec UFW
1. **Autoriser le trafic HTTP et HTTPS:**
```console
    sudo ufw allow 'Nginx Full'
```
###   Sur CentOS/RHEL avec firewalld
1. **Autoriser le trafic HTTP et HTTPS:**
```console
    sudo firewall-cmd --permanent --zone=public --add-service=http
```
```console
    sudo firewall-cmd --permanent --zone=public --add-service=https
```
```console
    sudo firewall-cmd --reload
```
## Étape 4: Configuration de base de Nginx
1.**Fichiers de configuration:**

Le fichier principal de configuration est situé à **/etc/nginx/nginx.conf**.
Cependant, il est souvent plus pratique de gérer les configurations de sites individuels
dans le répertoire **/etc/nginx/sites-available/** et de créer des liens symboliques vers **/etc/nginx/sites-enabled/**.


2. **Créer un fichier de configuration pour votre site:**

```console
    sudo nano /etc/nginx/sites-available/mon_site
```

3. **Exemple de configuration de base:**
```bash
    server {
        listen 80;
        server_name mon_site.com www.mon_site.com;

        root /var/www/mon_site;
        index index.html index.htm index.nginx-debian.html;

        location / {
            try_files $uri $uri/ =404;
        }
    }
```
4. **Activer la configuration:**
```console
    sudo ln -s /etc/nginx/sites-available/mon_site /etc/nginx/sites-enabled/
```
5. **Vérifier la configuration:**
```console
    sudo nginx -t
```
6. **Redémarrer Nginx:**
```console
    sudo systemctl reload nginx
```


## Étape 5: Déployer votre site web 

1. **Créer le répertoire de votre site:** 
```console
    sudo mkdir -p /var/www/mon_site
```
2. **Définir les permissions:**
```console
    sudo chown -R www-data:www-data /var/www/mon_site
```
```console
    sudo chmod -R 755 /var/www/mon_site
```
3. **Placer votre contenu dans le répertoire:**
```php
    echo "<html><h1>Bienvenue sur mon site</h1></html>" > /var/www/mon_site/index.html
```


# Guide de Configuration de FastCGI avec Nginx

## Étape 1: Installation des Paquets Nécessaires si PHP n'est pas installer sur votre serveur

### Sur Debian/Ubuntu

1. **Mettre à jour le système:**
```console
   sudo apt update
```
```console
   sudo apt upgrade
```
2. **Installer PHP-FPM**

```console 
    sudo apt install php-fpm
```

### Sur CentOS/RHEL

1. **Mettre à jour le système:**
```console
   sudo yum update
```
2. **Installer PHP-FPM**

```console 
    sudo yum install php-fpm
```


# Étape 2: Configurer PHP-FPM
1. **Modifier le fichier de configuration de PHP-FPM:**
Le fichier de configuration est généralement situé à /etc/php/8.1/fpm/php-fpm.conf (sur Debian/Ubuntu) ou /etc/php-fpm.d/www.conf (sur CentOS/RHEL).  
Assurez-vous que listen est configuré pour utiliser un socket Unix ou un port TCP. Par exemple:

```ini
    listen = /run/php/php8.1-fpm.sock
```

Soyez conscient que Sesi est pour PHP8.1. Le nom peut différer en fonction de la version PHP que vous utilisez.

2. **Démarrer et activer PHP-FPM:**
```console
    sudo systemctl start php8.1-fpm
```
```console
    sudo systemctl enable php8.1-fpm
```

# Étape 3: Configurer Nginx pour Utiliser FastCGI

1. **Créer un fichier de configuration pour votre site:** 

```console
    sudo nano /etc/nginx/sites-available/mon_site
```

2. **Ajouter la configuration FastCGI:**
Voici un exemple de configuration pour le serveur:
Dans les dossiers de votre site, créez un dossier api qui contiendra le code à appeler.

```ini
    server {
    server_name daedalus.younity-mc.fr;

    root /var/www/html/projet;
    index index.php index.html index.htm;

    # Page d'erreur 404
    error_page 404 /pages/404.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;

        # Si l'URL se termine par un /, on enlève le / automatiquement
        rewrite ^/(.+)/$ /$1 permanent;

        # Redirection vers la page de création de niveau
        rewrite ^/levels?/create$ /create last;

        # Redirection vers la page de création de niveau
        rewrite ^/levels?/(.+)/edit$ /create?id=$1 last;

        # Redirection vers la page de suppression de niveau
        rewrite ^/levels?/(.+)/delete$ /util/deleteLevel.php?id=$1 last;

        # Redirection d'URL pour la page de jeu
        rewrite ^/(level|adventure)s?/([^/]+)/play$ /play?id=$2&mode=$1 last;

        # Redirection d'URL pour le mode infini
        rewrite ^/infinite/play$ /play?mode=infinite last;

        # Redirection vers la page de présentation du niveau
        rewrite ^/levels?/(.+)$ /level?id=$1 last;

        # Redirection vers la page de profil
        rewrite ^/profile/(.+)$ /profile?id=$1 last;

        # Ajoute l'extension .php aux fichiers (si ce n'est pas un dossier ou un fichier existant)
        rewrite ^/([^.]+)$ /pages/$1.php last;
    }

    location /api/ {
        try_files $uri $uri/ /index.php?$query_string;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_index index.cgi;
        include fastcgi_params;
        fastcgi_pass unix:/var/run/fcgiwrap.socket;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    listen 443 ssl; # managed by Certbot
    ssl_certificate /etc/letsencrypt/live/daedalus.younity-mc.fr/fullchain.pem; # managed by Certbot
    ssl_certificate_key /etc/letsencrypt/live/daedalus.younity-mc.fr/privkey.pem; # managed by Certbot
    include /etc/letsencrypt/options-ssl-nginx.conf; # managed by Certbot
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem; # managed by Certbot
}

server {
    if ($host = isen.younity-mc.fr) {
        return 301 https://daedalus.younity-mc.fr$request_uri;
    } # managed by Certbot
    server_name isen.younity-mc.fr;
    return 404; # managed by Certbot

    listen 443 ssl; # managed by Certbot
    ssl_certificate /etc/letsencrypt/live/isen.younity-mc.fr/fullchain.pem; # managed by Certbot
    ssl_certificate_key /etc/letsencrypt/live/isen.younity-mc.fr/privkey.pem; # managed by Certbot
    include /etc/letsencrypt/options-ssl-nginx.conf; # managed by Certbot
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem; # managed by Certbot
}

server {
    if ($host = daedalus.younity-mc.fr) {
        return 301 https://$host$request_uri;
    } # managed by Certbot

    listen 80;
    server_name daedalus.younity-mc.fr;
    return 404; # managed by Certbot
}


```
3. **Activer la configuration:**
```console
    sudo ln -s /etc/nginx/sites-available/mon_site /etc/nginx/sites-enabled/
```

4. **Vérifier la configuration de Nginx:**
```console
    sudo nginx -t
```

5. **Redémarrer Nginx:**
```console
    sudo systemctl reload nginx
```


