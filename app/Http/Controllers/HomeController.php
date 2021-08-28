<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Auth::user()->admin) {
            $eAdmin = true;
            $garrafa = Auth::user()->garrafa;
        } else {
            $eAdmin = false;
            $garrafa = User::find(1)->garrafa;
        }

        $quantidade_ml_garrafa = $garrafa->quantidade_atual;
        $xicaras = $quantidade_ml_garrafa / $garrafa->capacidade_xicara;

        return view('home', compact('quantidade_ml_garrafa', 'xicaras', 'eAdmin'));
    }
}
