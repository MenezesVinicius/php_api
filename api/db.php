<?php

class DB
{
    // Parametros de conexão ao banco de dados
    var $servername = "mysql873.umbler.com:41890";
    var $username = "vcm";
    var $password = "vinicius22";
    var $dbname = "php_api";
    var $conn;

    function getConnstring()
    {
        $con = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname) or die("Connection failed: " . mysqli_connect_error());

        // Checa a conexao
        if (mysqli_connect_errno()) {
            printf("Conexão falhou: %s\n", mysqli_connect_error());
            exit();
        } else {
            $this->conn = $con;
            // Seta o charset do banco como utf8, senão dá erro na hora de realizar o json_encode
            // dos resultados obtidos do banco de dados
            mysqli_set_charset($this->conn, 'utf8');
        }
        return $this->conn;
    }
}

?>