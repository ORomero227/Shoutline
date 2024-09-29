<?php

namespace App\Http\Controllers;


use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View {
        return view('posts', []);
    }
}
