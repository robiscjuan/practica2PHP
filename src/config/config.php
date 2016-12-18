<?php   // config/config.php

/*
 * configuración SGBD
 */
define('DATABASE_HOST', '127.0.0.1');
define('DATABASE_PORT', 3306);
define('DATABASE_DBNAME', 'miw16_results');
define('DATABASE_USER', 'miw16');
define('DATABASE_PASSWD', '*miw16*');
define('DATABASE_DRIVER', 'pdo_mysql');
define('DATABASE_CHARSET', 'UTF8');

/*
 * configuración Doctrine
 */
define('PROXY_DIR', '/xampp/tmp');
define('ENTITY_DIR', __DIR__ . '/../Entity');
define('DEBUG', false);  // muestra consulta SQL por la salida estándar
