#!/bin/sh

php /var/www/html/api/setup.php
echo 'initialization done, starting apache'
exec apache2-foreground