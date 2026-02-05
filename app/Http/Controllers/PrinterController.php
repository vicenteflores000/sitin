<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PrinterController extends Controller
{
    public function index(): View
    {
        return view('admin.printers.index');
    }
}
