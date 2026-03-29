<?php

include (__DIR__ . "/../../../middleware/admin.php");

$sql = "SELECT * FROM genres";
$genres = $conn->query($sql);

$sql = "SELECT movies.*, GROUP_CONCAT(genres.name SEPARATOR ', ') AS genres_names
    FROM movies
    LEFT JOIN movie_genres ON movies.id = movie_genres.movie_id
    LEFT JOIN genres ON movie_genres.genre_id = genres.id
    GROUP BY movies.id";

$movies = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Filmes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
    <header class="text-center border-bottom p-1">
        <h1>Gerenciamento de Filmes</h1>
    </header>
    <main class="d-flex flex-column align-items-center" style="height: calc(100vh - 65px);">
        <section class="container w-25">
            <div class="d-flex align-items-center justify-content-center gap-2 mt-5">
                <a href="../../../views/admin/" class="btn btn-danger">Voltar</a>
                <h2>Cadastrar Filme</h2>
            </div>
            <form action="../../../controllers/movies/create.php" method="POST" class="text-center border p-3 rounded mb-5 mt-3" enctype="multipart/form-data">
                <span><?= $_SESSION['message'] ?></span>
                <div class="text-start">
                    <label for="title">Titulo do filme</label>
                    <input class="form-control mb-3" type="text" name="title" placeholder="Meu Filme Legal" id="title" required>
                </div>
                <div class="text-start">
                    <label for="desc">Sinopse</label>
                    <input class="form-control mb-3" type="text" name="desc" id="desc" placeholder="Lorem ipsum dolor sit amet..." required>
                </div>
                <div class="d-flex gap-3">
                    <div class="text-start w-50">
                        <label for="genre">Gênero</label>
                        <select class="form-select mb-3" name="genre[]" id="genre" multiple size="1" class="select">
                            <?php while($genre = $genres->fetch_assoc()): ?>
                                <option value="<?= $genre['id'] ?>"><?= $genre['name'] ?></option>
                            <?php endwhile ?>
                        </select>
                    </div>
                    <div class="text-start w-50">
                        <label for="release">Ano de Lançamento</label>
                        <input class="form-control mb-3" type="number" id="release" name="release_yr" min="1888" max="2026" maxlenght="4" placeholder="1945" required>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <div class="text-start w-50">
                        <label for="min">Duração em Minutos</label>
                        <input class="form-control mb-3" type="number" id="min" name="duration" placeholder="120" required>
                    </div>
                    <div class="text-start w-50">
                        <label for="age">Class. Indicativa</label>
                        <input class="form-control mb-3" type="number" id="age" name="age_rating" max="18" maxlenght="2" placeholder="12" required>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <div class="text-start w-50">
                        <label for="poster">Poster</label>
                        <input class="form-control mb-3" id="poster" type="file" name="poster" accept="image/*">
                    </div>
                    <div class="text-start w-50">
                        <label for="banner">Banner</label>
                        <input class="form-control mb-3" id="banner" type="file" name="poster" accept="image/*">
                    </div>
                </div>
                <div class="text-start">
                    <label for="trailer">Trailer</label>
                    <input class="form-control mb-3" type="text" name="trailer_url" placeholder="Link do Trailer (YouTube)">
                </div>
                <button type="submit" class="btn btn-primary">Cadastrar</button>
            </form>
        </section>
        <aside class="container w-50">
            <h2>Filmes Cadastrados</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Poster</th>
                        <th>Nome</th>
                        <th>Sinopse</th>
                        <th>Gênero</th>
                        <th>Ano de lançamento</th>
                        <th>Duração (min)</th>
                        <th>Classificação Indicativa</th>
                        <th>Alugado</th>
                        <th>Alugado em</th>
                        <th>Aluguel termina em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($movie = $movies->fetch_assoc()):
                        if($movie['age_rating'] < 10){ 
                            $class = 'Livre';
                        } else { $class = $movie['age_rating'];}
                        if($movie['rented'] == TRUE){ 
                            $alugado = 'Sim'; 
                        } else { 
                            $alugado = 'Nao';
                        } ?>
                        <tr>
                            <td><?= $movie['id'] ?></td>
                            <td><img src="../../../assets/uploads/posters/interestellar.png" alt="Poster1" style="height: 100px"></td>
                            <td><?= $movie['title'] ?></td>
                            <td><?= $movie['description'] ?></td>
                            <td><?= $movie['genres_names'] ?></td>
                            <td><?= $movie['release_yr'] ?></td>
                            <td><?= $movie['duration'] ?></td>
                            <td><?= $class ?></td>
                            <td><?= $alugado ?></td>
                            <td><?= $movie['rent_start_date'] ?? '-' ?></td>
                            <td><?= $movie['rent_end_date'] ?? '-' ?></td>
                            <td>
                                <a class="btn btn-warning" href="update.php?id=<?= $movie['id'] ?>">Editar</a>
                                <a class="btn btn-danger" href="../../../controllers/movies/delete.php?id=<?= $movie['id'] ?>">Excluir</a>
                            </td>
                        </tr>
                    <?php endwhile ?>
                </tbody>
            </table>
        </aside>
    </main>
</body>
</html>