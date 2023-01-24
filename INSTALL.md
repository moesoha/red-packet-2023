# install

```shell
apt-get install \
	bind9 bird2 openvpn nginx nodejs \
	composer php8.2-{fpm,gmp,xml,pgsql,mbstring} postgresql-14 \
	unzip curl \
	net-tools # openvpn requires this
	
corepack enable
corepack prepare pnpm@latest --activate
```

# networking

add `fd23:fbac:da7a:1:53::/128` & `fd23:fbac:da7a:1:3eb::/128` to lo.

add `fd23:fbac:da7a::/48` route to local.

# commands

```shell
cd /opt
git clone <...> red-packet-2023
cd red-packet-2023
cd level2
pnpm -P install
cd ../..
cd web
composer install

cd /etc/systemd/system
ln -s /opt/red-packet-2023/level2/hb2023-lv2-reviewer.service
ln -s /opt/red-packet-2023/level2/hb2023-lv2-reviewer.timer

cd /etc/bind
vim named.conf.options
vim named.conf.local # include "/opt/red-packet-2023/level3/web.bind.conf";

cd /etc/bird
ln -s /opt/red-packet-2023/level3/bird.conf

cd /etc/openvpn
ln -s /opt/red-packet-2023/level3/server.conf yjsvpn.conf
systemctl enable --now openvpn@yjsvpn.service

cd /etc/nginx/conf.d
vim <...>
```

# nginx

```
limit_req_zone $binary_remote_addr zone=hb2023:100m rate=1r/s;
limit_req zone=hb2023 burst=3 nodelay;
server {
	listen 0.0.0.0:443 ssl http2;
	listen [::]:443 ssl http2;
	server_name tnhbyjs.hb.lohu.info;
	root /opt/red-packet-2023/web/public;

	access_log /var/log/nginx/hb2023.access.log;
	error_log /var/log/nginx/hb2023.error.log;

	location / {
		try_files $uri /index.php$is_args$args;
	}

	location ~ ^/index\.php(/|$) {
		internal;
		limit_req zone=hb2023 burst=3 nodelay;
		fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
		fastcgi_split_path_info ^(.+\.php)(/.*)$;
		include fastcgi_params;
		fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
		fastcgi_param DOCUMENT_ROOT $realpath_root;
	}

	location ~ \.php$ {
		return 404;
	}
}

server {
	listen [::1]:8082;
	root /opt/red-packet-2023/web/public;

	access_log /var/log/nginx/hb2023-local.access.log;
	error_log /var/log/nginx/hb2023-local.error.log;

	location / {
		try_files $uri /index.php$is_args$args;
	}

	location ~ ^/index\.php(/|$) {
		internal;
		fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
		fastcgi_split_path_info ^(.+\.php)(/.*)$;
		include fastcgi_params;
		fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
		fastcgi_param DOCUMENT_ROOT $realpath_root;
	}

	location ~ \.php$ {
		return 404;
	}
}
```
