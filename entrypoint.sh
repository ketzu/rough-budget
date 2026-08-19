#!/bin/sh
set -eu

php /usr/local/bin/setup.php
printf 'window.__ROUGH_BUDGET_CONFIG__ = ' > /var/www/html/config.js
php -r 'echo json_encode(["privacy" => ["responsible" => getenv("PRIVACY_RESPONSIBLE") ?: "", "address" => getenv("PRIVACY_ADDRESS") ?: "", "email" => getenv("PRIVACY_EMAIL") ?: "", "phone" => getenv("PRIVACY_PHONE") ?: ""]], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);' >> /var/www/html/config.js
printf ';\n' >> /var/www/html/config.js
echo 'initialization done, starting apache'
exec apache2-foreground
