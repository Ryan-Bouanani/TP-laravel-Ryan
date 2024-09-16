@extends('base')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $plat->name }}</div>

                    <div class="card-body">
                        <img src="{{ asset($plat->image) }}" alt="{{ $plat->nom }}" class="img-fluid">
                        <p><strong>Recette :</strong> {{ $plat->description }}</p>
                        <p><strong>Auteur :</strong> {{ $plat->user->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
