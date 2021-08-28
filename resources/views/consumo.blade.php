@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Relatório de consumo de café</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xs-1">
                                <img src="{{ asset('img/garrafa.png') }}" width="200"/>
                            </div>
                            <div class="col">
                                <table class="table">
                                    <tr>
                                        <th>Usuário</th>
                                        <th>Quantidade</th>
                                        <th></th>
                                    </tr>
                                    @foreach($consumos as $consumo)
                                        <tr>
                                            <td>
                                                <p>{{ $consumo->name }}</p>
                                            </td>
                                            <td>
                                                <p>{{ $consumo->total }}</p>
                                            </td>
                                            <td>
                                                @if($consumo->total >= 5 )
                                                    <div class="alert alert-warning" role="alert">Esse cabra toma muito café!</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                                <a href="{{ route('home') }}" class="btn btn-primary">Ir para o início</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
