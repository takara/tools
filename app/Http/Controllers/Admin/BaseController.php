<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
	pulic function __construct()
	{
        $this->middleware('auth');
	}
}
