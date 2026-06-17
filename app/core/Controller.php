<?php
defined('ORION') || exit('Acesso negado.');

abstract class Controller
{
    protected function view($view, array $data = [], $layout = 'admin')
    {
        extract($data, EXTR_SKIP);

        $viewFile = VIEW_PATH . '/' . $view . '.php';
        if (!is_file($viewFile)) {
            exit('View não encontrada: ' . e($view));
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require VIEW_PATH . '/layouts/' . $layout . '.php';
    }

    protected function guardPost()
    {
        if (!is_post()) {
            redirect();
        }
        if (!verify_csrf()) {
            flash('danger', 'Sessão expirada ou requisição inválida. Tente novamente.');
            redirect();
        }
    }

    protected function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
}
