<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class NotFoundController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Auth/NotFound')
            ->toResponse($request)
            ->setStatusCode(404);
    }
}
