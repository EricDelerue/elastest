;<?php die(); ?>
;
;This is the Elastest API ini file setup
;

[version_info]
version_number = 2.0
version_stable = 2.0
last_updated = 05/09/2018

[development_db_info]
db_hostname = 127.0.0.1
db_name = elastest
db_user = root
db_password = 
db_port = 3306
db_socket = 

[development_url_info]
api_base_url = https://127.0.0.1/elastique
api_base_directory = /api/
api_base_version = v2.0

[development_cache_info]
cache_enabled = true   		    
cache_type = TempFile
cache_timeout = 10
cache_path = C:\Users\Surface\xampp\htdocs\elastique\cache 

[production_db_info]
db_hostname = localhost
db_name = elastest
db_user = eric
db_password = biddicchia
db_port = 3306
db_socket = 

[production_url_info]
api_base_url = https://ericdelerue.com/elastique
api_base_directory = /api/
api_base_version = v2.0

[production_cache_info]
cache_enabled = true     		    
cache_type = TempFile
cache_timeout = 10
cache_path = /var/www/html/dev.ericdelerue.com/elastique/cache 
