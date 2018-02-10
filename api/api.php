<?php

require_once 'base_api.php';
require_once 'db.php';

class API extends base_API
{
    private $connection = null;
    private $response = array();

    public function __construct($request)
    {
        parent::__construct($request);
        $db = new DB();
        $this->connection = $db->getConnstring();
    }

    // Endpoint para realizar operações crud na entidade usuários
    protected function usuarios($id)
    {
        $this->response = array("endpoint" => $this->endpoint, "args" => $this->args, "request" => $this->request);

        switch ($this->method) {
            case "GET":
                if (!empty($id)) {
                    $usuario_id = intval($id);
                    return $this->get_usuarios($usuario_id);
                } else {
                    return $this->get_usuarios();
                }
                break;

            case "POST":
                return $this->insert_usuario();
                break;

            case "PUT":
                $usuario_id = intval($id);
                return $this->update_usuario($usuario_id);
                break;

            case "DELETE":
                $usuario_id = intval($id);
                return $this->delete_usuario($usuario_id);
                break;
            default:
                break;
        }
    }

    private function get_usuarios($usuario_id = 0)
    {
        $query = "SELECT * FROM usuarios";
        if ($usuario_id != 0) {
            $query .= " WHERE id=" . $usuario_id . " LIMIT 1";
        }
        if (($result = mysqli_query($this->connection, $query))) {
            $this->response['status'] = 'sucesso';
            $r = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $r[] = $row;
            }
            $this->response['result'] = $r;
        } else {
            $this->response['status'] = 'falha';
        }

        header('Content-Type: application/json');
        return $this->response;
    }

    private function insert_usuario()
    {
        $login = $_POST["login"];
        $nome = $_POST["nome"];
        $cpf = $_POST["cpf"];
        $email = $_POST["email"];
        $endereco = $_POST["endereco"];
        $senha = $_POST["senha"];

        $this->response["status"] = "sucesso";

        if (!empty($login) && !empty($nome) && !empty($cpf) && !empty($email) && !empty($endereco) && !empty($senha)) {
            $query = "INSERT INTO usuarios (login, nome, cpf, email, endereco, senha) VALUES ('$login', '$nome', '$cpf', '$email', '$endereco', '$senha')";
            if (!mysqli_query($this->connection, $query)) {
                $this->response["status"] = "falha";
            }
        } else {
            $this->response["status"] = "falha";
        }

        header('Content-Type: application/json');
        return $this->response;
    }

    private function update_usuario($usuario_id)
    {
        $this->response["status"] = "sucesso";
        $this->response["put_vars"] = $this->put_vars;

        if (!empty($usuario_id)) {

            $query = "SELECT * FROM usuarios WHERE id =" . $usuario_id . " LIMIT 1";
            $result = mysqli_query($this->connection, $query);

            if ($result->num_rows > 0) {

                $columns = array("login",
                    "nome",
                    "cpf",
                    "email",
                    "endereco",
                    "senha");

                // Verifica quais campos não são nulos para realizar o update
                foreach ($this->put_vars as $key => $value) {
                    if (in_array($key, $columns) && $value != '') {
                        $set[] = $key . " = '" . mysqli_real_escape_string($this->connection, $value) . "'";
                    }
                }

                if (!empty($set)) {
                    $query = "UPDATE usuarios SET " . implode(', ', $set) . " WHERE id = '" . mysqli_real_escape_string($this->connection, $usuario_id) . "'";
                }

                if (!mysqli_query($this->connection, $query)) {
                    $this->response["status"] = "falha";
                }
            } else {
                $this->response["status"] = "falha";
            }

        } else {
            $this->response["status"] = "falha";
        }

        header('Content-Type: application/json');
        return $this->response;
    }

    private function delete_usuario($usuario_id)
    {
        $this->response["status"] = "sucesso";
        if (!empty($usuario_id)) {
            $query = "DELETE FROM usuarios WHERE id=" . $usuario_id;
            if (!mysqli_query($this->connection, $query)) {
                $this->response["status"] = "falha";
            }
        } else {
            $this->response["status"] = "falha";
        }
        header('Content-Type: application/json');
        return $this->response;
    }
}

?>