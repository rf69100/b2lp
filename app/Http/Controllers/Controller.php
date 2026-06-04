<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    // Permet d'utiliser $this->authorize(...) dans les contrôleurs (délègue aux Policies).
    use AuthorizesRequests;
}
