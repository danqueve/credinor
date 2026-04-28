<?php
namespace App\Controllers;

class ManualController extends \App\Core\Controller
{
    // GET /manual
    public function index(): void
    {
        $this->requireRole(['admin', 'vendedor', 'cobrador']);
        $this->view('manual', [], 'manual');
    }
}
