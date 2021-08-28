<?php

namespace App\Http\Controllers;

use App\Consumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsumoController extends Controller
{
    public function index()
    {
        $consumos = DB::table('consumos')
                        ->join('users', 'consumos.user_id', 'users.id')
                        ->select('user_id', 'name', DB::raw('count(*) as total'))
                        ->groupBy('user_id')
                        ->orderBy('total', 'DESC')
                        ->get();

        return view('consumo', compact('consumos'));
    }
}
