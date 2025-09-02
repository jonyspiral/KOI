<?php
@ini_set('log_errors', 1);
@ini_set('error_log', '/var/www/encinitas/logs/php_errors.log');
error_log('[PING] '.date('Y-m-d H:i:s').' ok');
echo 'ok';
