@extends('base')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Modifier le plat</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('plats.update', $plat->slug) }}">
                            @csrf
                            @method('PUT')
                            {{--  Nom du plats --}}
                            <div class="form-group">
                                <label for="name">Nom du plat</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $plat->name) }}">
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            {{--  Recette du plats --}}
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" class="form-control @error("description") is-invalid @enderror"  name="description">{{ old('description', $plat->description) }}</textarea>
                                @error("description")
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            {{--  Lien image du plats --}}
                            <div class="form-group">
                                <label for="image">Lien image</label>
                                <input type="text" class="form-control @error("image") is-invalid @enderror" id="image" name="image" value="{{ old('image', $plat->image) }}">
                                @error("image")
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            {{--  Créateur du plats --}}
                            <div class="form-group">
                                <label for="user_id">Créateur du plat</label>
                                <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ $plat->user_id == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Modifier</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
