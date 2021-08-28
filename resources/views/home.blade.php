@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Garrafa de café</div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if (session('warning'))
                            <div class="alert alert-warning" role="alert">
                                {{ session('warning') }}
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-xs-1">
                                <img src="{{ asset('img/garrafa.png') }}" width="200"/>
                            </div>
                            <div class="col">
                                @if($xicaras == 0)
                                    <p>
                                        A garrafa não tem café. :(<br>
                                        @if($eAdmin)
                                            Coloque café para as pessoas poderem beber!
                                        @else
                                            Peça para o administrador colocar mais café!
                                        @endif
                                    </p>
                                @else
                                    <p>A garrafa tem {{ $xicaras }} xícaras de café ({{ $quantidade_ml_garrafa }}
                                        ml).</p>
                                @endif

                                @if($eAdmin)
                                    <form method="post" action="{{ route('encher') }}" style="display: inline">
                                        @csrf
                                        <p>Colocar mais <input type="number" name="xicaras"> xícaras.</p>
                                        <button type="submit" class="btn btn-success">Adicionar xícaras</button>
                                    </form>
                                    <a href="{{ route('consumo') }}" class="btn btn-secondary">Ver consumo</a>
                                @endif

                                @if($xicaras > 0)
                                    <a href="{{ route('beber') }}" class="btn btn-primary">Beber uma xícara de café</a>
                                @else
                                    @if(!$eAdmin)
                                        <p>Não há xícaras de café disponíveis.</p>
                                    @endif
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
