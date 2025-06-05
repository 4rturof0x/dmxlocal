<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use PDO;

class HomeController {
    private Twig $view;
    private $db;


    public function __construct(Twig $view, PDO $db) {
        $this->view = $view;
        $this->db = $db;
    }

    public function index(Request $request, Response $response): Response {
        return $this->view->render($response, 'home.twig', [
            'mensaje' => 'Hola desde Slim 4 con Twig',
        ]);
    }

    public function usuarios(Request $request, Response $response): Response
    {
        $stmt = $this->db->query('SELECT * FROM usuarios');
        $usuarios = $stmt->fetchAll();

        return $this->view->render($response, 'usuarios.twig', [
            'usuarios' => $usuarios,
        ]);
    }

}