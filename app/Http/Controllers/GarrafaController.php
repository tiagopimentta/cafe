<?php

namespace App\Http\Controllers;

use App\Consumo;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class GarrafaController extends Controller
{
    public function encher(Request $request, Response $response)
    {
        $xicarasParaEncher = $request->xicaras;

        if (!is_numeric($xicarasParaEncher) || (int) $xicarasParaEncher < 1) {
            return redirect()->route('home')->with('warning', 'Você deve informar a quantidade de café!');
        }

        $garrafa = Auth::user()->garrafa;
        $capacidadeGarrafa = $garrafa->capacidade_total;
        $quantidadeAtual = $garrafa->quantidade_atual;
        $capacidadeDaXicara = $garrafa->capacidade_xicara;

        $quantidadeAposEncher = $quantidadeAtual + ($xicarasParaEncher * $capacidadeDaXicara);

        if ($capacidadeGarrafa < $quantidadeAposEncher) {

            $garrafa->update([
                'quantidade_atual' => $capacidadeGarrafa, // enche o máximo
            ]);

            $quantidadeMlQueDerramou = $quantidadeAposEncher - $capacidadeGarrafa;
            $xicarasQueDerramaram = $quantidadeMlQueDerramou / $capacidadeDaXicara;

            $msg = 'Você colocou mais xícaras de café do que cabem na garrafa! ' .
                    "Você derramou $xicarasQueDerramaram xícaras de café ($quantidadeMlQueDerramou ml)";

            $request->session()->flash('error', $msg);

            return redirect()->route('home');
        }

        $garrafa->update([
            'quantidade_atual' => $quantidadeAposEncher
        ]);

        return redirect()->route('home')->with('success', "Você colocou mais $xicarasParaEncher xícaras de café na garrafa!");
    }

    public function beber(Request $request, Response $response)
    {
        $garrafa = User::find(1)->garrafa;

        $garrafa->update([
            'quantidade_atual' => $garrafa->quantidade_atual - $garrafa->capacidade_xicara
        ]);

        Auth::user()->consumos()->save(new Consumo());

        $totalConsumo = Auth::user()->consumos()->count();

        if ($totalConsumo <= $garrafa->limite_cafe) {
            return redirect()->route('home')->with('success', "Você bebeu uma xícara de café!");
        }

        return redirect()->route('home')->with('warning', 'Você está bebendo muito café!');
    }
}
