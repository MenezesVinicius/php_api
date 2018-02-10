<?php

abstract class base_API
{
    protected $method = '';
    protected $endpoint = '';
    protected $args = array();
    protected $put_vars = null;
    protected $request = null;

    public function __construct($request)
    {
        // Resolver problema de CORS
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT");         

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }

        // Recebe os argumentos inseridos após o endpoint, ex: id
        $this->args = explode('/', rtrim($request, '/'));
        // O endpoint em questão, ex: usuarios
        $this->endpoint = array_shift($this->args);

        // Grava qual método foi chamado: GET, POST, PUT ou DELETE
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function processAPI()
    {
        switch ($this->method) {
            case 'DELETE':
            case 'POST':
                $this->request = $this->_cleanInputs($_POST);
                break;
            case 'GET':
                $this->request = $this->_cleanInputs($_GET);
                break;
            case 'PUT':
                $this->request = $this->_cleanInputs($_GET);
                parse_str(file_get_contents("php://input"), $this->put_vars);
                break;
            default:
                return $this->_response('Invalid Method', 405);
                break;
        }

        if ((int)method_exists($this, $this->endpoint) > 0) {
            // Se o método relacionado ao endpoint inserido existe,
            // o método é chamado e o resultado é enviado para a função de resposta
            $id = null;
            if (!empty($this->args)) {
                $id = $this->args[0];
            }
            return $this->_response($this->{$this->endpoint}($id));
        }

        return $this->_response("No Endpoint: $this->endpoint", 404);
    }

    private function _response($data, $status = 200)
    {
        header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    private function _cleanInputs($data)
    {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->_cleanInputs($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }

    private function _requestStatus($code)
    {
        $status = array(
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return ($status[$code]) ? $status[$code] : $status[500];
    }
}

?>