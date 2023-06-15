<?php

$config = parse_ini_file('db_config.ini');

$tns = "  
(DESCRIPTION =
        (ADDRESS_LIST =
        (ADDRESS = (PROTOCOL = TCP)(HOST = " . $config['dbhost'] . ")(PORT = " . $config['dbport'] . "))
        )
        (CONNECT_DATA =
        (SERVICE_NAME = " . $config['dbname'] . ")
        )
)";

try {
    $db = new PDO($config['dbtype'] . ":dbname=" . $tns, $config['dbuser'], $config['dbpassword']);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
    die;
}
