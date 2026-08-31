<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        $projects = Project::query()
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->get();

        return view('home', ['projects' => $projects]);
    }
}
