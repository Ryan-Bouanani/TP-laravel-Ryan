@extends('base')

@section('content')
    @if (session('danger'))
        <div class="alert mt-3 alert-danger">
            {{ session('danger') }}
        </div>
    @elseif(session('success'))
        <div class="alert mt-3 alert-success">
            {{ session('success') }}
        </div>
    @endif
    <table class="table table-striped mt-5">
        <thead>
        <tr>
            <th scope="col">Favoris</th>
            <th scope="col">Titre</th>
            <th scope="col">Créateur</th>
            <th scope="col">Likes</th>
            <th scope="col">Détails</th>
            <th scope="col">Modifier</th>
            <th scope="col">Supprimer</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($plats as $plat)
            <tr>
                <td>
                    <form action="{{ route('addFavoritePlatToUser', Auth::user()->id) }}" method="POST">
                        @csrf
                        <input type ="hidden" name="plat_id" value="{{$plat->id}}">

                        @if(auth()->user()->favoritePlats()->where('plat_id', $plat->id)->exists())
                            <button type="submit" class="add-to-favorites" >Retiré des favoris</button>
                        @else
                            <button type="submit" class="add-to-favorites" >Ajouter aux favoris</button>
                        @endif
                    </form>
                </td>
                <td>{{ $plat->name }}</td>
                <td>{{ $plat->user->name }}</td>
                <td>{{ $plat->favoriteByUsers->count() }}</td>
                <td>
                    <a href="{{ route('plats.show', $plat->slug) }}">Voir</a>
                </td>
                <td>
                    <a href="{{ route('plats.edit', $plat->slug) }}">Modifier</a>
                </td>
                <td>
                    <form action="{{ route('plats.delete', $plat->slug) }}" method="POST">
                        @csrf
                        @method("DELETE")
                        <button type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <a href="{{ route('plats.create') }}" class="btn btn-primary">Créer</a>

        <ul class="pagination justify-content-center">
            {{ $plats->links() }}
        </ul>

@endsection

