<?php

namespace App\Http\Controllers;

class NotificationController extends Controller
{
    public function index()
    {
        return inertia('Notification/Index', [
            'notifications' => auth()->user()->notifications()->paginate(10),
        ]);
    }
}
