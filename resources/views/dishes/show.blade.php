@extends('base')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $dish->name }}</div>

                    <div class="card-body">
                        <img src="{{ asset($dish->image) }}" alt="{{ $dish->nom }}" class="img-fluid">
                        <p><strong>Recette :</strong> {{ $dish->description }}</p>
                        <p><strong>Auteur :</strong> {{ $dish->user->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
