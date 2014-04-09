

1、做配置软链
cd /usr/local/nginx/html/MoonApi/application/
ln -s config_production config

2、nginx配置
server {
    listen       80;
    index   index.php;
    server_name moon.any.local;
    root  /usr/local/nginx/html/MoonApi/application/;

    client_max_body_size 20m;

    access_log  /data/logs/moon.any.local.access.log;
    error_log   /data/logs/moon.any.local.error.log;

    location / {
        if (-f $request_filename) {
            expires max;
            break;
        }

        if ($request_filename !~ (js|css|robots/.txt|index/.php.*) ) {
            rewrite ^/(.*)$ index.php last;
            break;
        }
    }

    location ~* index\.php {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_split_path_info ^(.+\.php)(.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/$fastcgi_script_name;
    }

}