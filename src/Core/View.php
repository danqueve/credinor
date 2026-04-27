<?php
// src/Core/View.php
namespace App\Core;

class View
{
    private string $layout = 'app';
    private array $data = [];
    private string $content = '';

    public function __construct(private string $view) {}

    public function with(array $data): static
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    public function layout(string $layout): static
    {
        $this->layout = $layout;
        return $this;
    }

    public function render(): void
    {
        // Extraer variables al scope local
        extract($this->data, EXTR_SKIP);

        // Capturar la vista parcial
        ob_start();
        $viewFile = ROOT_PATH . '/views/' . str_replace('.', '/', $this->view) . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("Vista no encontrada: {$viewFile}");
        }
        require $viewFile;
        $content = ob_get_clean();

        // Renderizar con layout
        $layoutFile = ROOT_PATH . '/views/layouts/' . $this->layout . '.php';
        require $layoutFile;
    }

    /** Helper estático */
    public static function make(string $view, array $data = [], string $layout = 'app'): void
    {
        (new self($view))->with($data)->layout($layout)->render();
    }
}
