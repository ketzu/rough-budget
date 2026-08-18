#!/bin/sh
set -eu

php /usr/local/bin/setup.php
echo 'initialization done, starting apache'
exec apache2-foreground
