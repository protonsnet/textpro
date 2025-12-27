<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\PlanModel;

class HomeController extends Controller
{
    public function index(): void
    {
        $planModel = new PlanModel();
        $planos = $planModel->allActive();

        $this->viewPublic('home/index', [
            'title'  => 'TextPro — Documentos ABNT',
            'planos' => $planos
        ]);
    }
}
