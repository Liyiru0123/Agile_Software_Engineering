<?php

namespace App\Http\Controllers;

// Import Laravel core controller
use Illuminate\Routing\Controller as BaseController;
// Import essential traits for permission verification and form validation
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

// Inherit core controller to enable core methods for all child controllers
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}