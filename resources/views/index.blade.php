@extends('base')

@section('content')
    <!-- Formulaire de filtrage -->
    <form action="{{ route('dishes.index') }}" method="GET" class="mb-4 mt-5">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="name" class="form-control" placeholder="Nom du plat"
                       value="{{ request('name') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="creator" class="form-control" placeholder="Nom du créateur"
                       value="{{ request('creator') }}">
            </div>
            <div class="col-md-3">
                <input type="number" name="min_likes" class="form-control" placeholder="Likes minimum"
                       value="{{ request('min_likes') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="{{ route('dishes.index') }}" class="btn btn-secondary">Réinitialiser</a>
            </div>
        </div>
    </form>

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
            <th scope="col">
                <a href="{{ route('dishes.index', array_merge(request()->query(), ['sort' => 'id', 'order' => ($sortField == 'id' && $sortOrder === 'asc') ? 'desc' : 'asc'])) }}">Id</a>
            </th>
            <th scope="col">Favoris</th>
            <th scope="col"><a
                    href="{{ route('dishes.index', array_merge(request()->query(), ['sort' => 'name', 'order' => ($sortField === 'name' && $sortOrder === 'asc') ? 'desc' : 'asc'])) }}">Titre</a>
            </th>
            <th scope="col"><a
                    href="{{ route('dishes.index', array_merge(request()->query(), ['sort' => 'creator', 'order' => ($sortField === 'creator' && $sortOrder === 'asc') ? 'desc' : 'asc'])) }}">Créateur</a>
            </th>

            <!--['name'=>request('name')-->
            <th scope="col">
                <a href="{{ route('dishes.index', array_merge(request()->query(), ['sort' => 'likes', 'order' => ($sortField === 'likes' && $sortOrder === 'asc') ? 'desc' : 'asc'])) }}">Likes</a>
            </th>
            <th scope="col">Détails</th>
            <th scope="col">Modifier</th>
            <th scope="col">Supprimer</th>
        </tr>
        </thead>
        <tbody>
        @if(!$dishes->isEmpty())
            @foreach ($dishes as $dish)
            <tr>
                <td>{{ $dish->id }}</td>
                <td>
                    <form action="{{ route('toggleFavoriteDish', Auth::user()->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="dish_id" value="{{$dish->id}}">

                        @if(auth()->user()->favoriteDishes()->where('dish_id', $dish->id)->exists())
                            <button type="submit" class="add-to-favorites">Retiré des favoris</button>
                        @else
                            <button type="submit" class="add-to-favorites">Ajouter aux favoris</button>
                        @endif
                    </form>
                </td>
                <td>{{ $dish->name }}</td>
                <td>{{ $dish->user->name }}</td>
                <td>{{ $dish->favoriteByUsers->count() }}</td>
                <td>
                    <a href="{{ route('dishes.show', $dish->slug) }}">Voir</a>
                </td>
                <td>
                    @if(Auth::user()->hasRole('admin') || $dish->user->id === Auth::id())
                        <a href="{{ route('dishes.edit', $dish->slug) }}">Modifier</a>
                    @endif
                </td>
                <td>
                    <form action="{{ route('dishes.destroy', $dish->slug) }}" method="POST">
                        @csrf
                        @method("DELETE")
                        <button type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
        @endforeach
        @else
            <tr><td colspan="8">Aucun plats n'a été trouvés</td></tr>
        @endif
        </tbody>
    </table>

    <a href="{{ route('dishes.create') }}" class="btn btn-primary">Créer</a>

    <ul class="pagination justify-content-center">
        {{ $dishes->links() }}
    </ul>

@endsection

