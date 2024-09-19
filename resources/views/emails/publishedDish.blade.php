<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">
        <title>Plat confirmé </title>
    </head>
    <body>
    <div class="container">

        <div class="message">
            <p>Bonjour, {{ $user->name }}</p>
            <p>Votre plat : "{{ $dish->name }}" à bien été crée !</p>
        </div>
    </div>
    </body>
</html>
