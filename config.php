<?php
return [
                'host' =>  $_ENV['DB_HOST'],
                'name' => $_ENV['DB_DATABASE']  ,
                'user' =>  $_ENV['DB_USERNAME'],
                'pass' => $_ENV['DB_PASSWORD'] ,
                'charset' => $_ENV['DB_CHARSET']
            ];
