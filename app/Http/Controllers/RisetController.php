<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RisetController extends Controller
{
    public function index() {
        return view('riset.index');
    }

    public function riset(string $slug) {
        $view = "riset.$slug";
        abort_if(!view()->exists($view), 404);
        return view($view);
    }
}
