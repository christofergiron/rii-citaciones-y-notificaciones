#!/usr/bin/env bash

cat <<EOF > Dockerrun.aws.json
{
   "AWSEBDockerrunVersion": 2,
   "containerDefinitions": [
     {
       "essential": true,
       "image": "$REPOSITORY_URI/php-fpm:$IMAGE_TAG",
       "memory": 256,
       "mountPoints": [
         {
           "containerPath": "/var/www/html",
           "sourceVolume": "application"
         },
         {
           "containerPath": "/usr/local/var/log",
           "sourceVolume": "awseb-logs-php-fpm"
         }
       ],
       "name": "php-fpm"
     },
     {
       "essential": true,
       "image": "$REPOSITORY_URI/nginx:$IMAGE_TAG",
       "links": [
         "php-fpm"
       ],
       "memory": 512,
       "mountPoints": [
         {
           "containerPath": "/var/www/html",
           "sourceVolume": "application"
         },
         {
           "containerPath": "/var/log/nginx",
           "sourceVolume": "awseb-logs-nginx"
         },
         {
           "containerPath": "/etc/nginx/sites-available",
           "sourceVolume": "nginx-proxy-sites"
         }
       ],
       "name": "nginx",
       "portMappings": [
         {
           "containerPort": 80,
           "hostPort": 80
         }
       ]
     }
   ],
   "volumes": [
     {
       "host": {
         "sourcePath": "/var/app/current/application"
       },
       "name": "application"
     },
     {
      "name": "nginx-proxy-sites",
      "host": {
        "sourcePath": "/var/app/current/proxy/sites"
      }
    }
   ]
 }
EOF