<?php

// Arquivo que contém os métodos para o endpoint usuários
require_once "api.php";

try {
    // $_REQUEST['request'] vem da regra contida no arquivo .htaccess
    $API = new API($_REQUEST['request']);
    echo $API->processAPI();
}
catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}

?>