<?php

include (__DIR__ . "/../../middleware/admin.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // Tentando usar função pra aprender
    function fileUpload($file, $subpasta) {
        // Se não enviaram arquivo ou deu erro no upload, para aqui
        if (empty($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        // Pega a extensão original
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        // Cria um nome único usando o tempo atual + um número aleatório
        $fileName = time() . "_" . rand(1000, 9999) . "." . $extension;
        // Cria o caminho pro assets
        $filePath = "../../assets/uploads/" . $subpasta . "/" . $fileName;
        // Move o arquivo para a pasta definitiva
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return $fileName; // Retorna o nome para salvar no banco
        }
        return null;
    }

    $title = $_POST['title'] ?? '';
    $desc = $_POST['desc'] ?? '';
    $genres = $_POST['genre'] ?? [];
    $release_yr = $_POST['release_yr'] ?? '';
    $duration = $_POST['duration'] ?? '';
    $age_rating = $_POST['age_rating'] ?? '';
    $trailer = $_POST['trailer_url'] ?? '';

    if(empty($title) || empty($desc) || empty($genres) || empty($release_yr) || empty($duration) || empty($age_rating) || empty($trailer)){
        $_SESSION['message'] = 'Por favor, preencha os campos corretamente.';
        header("Location: ../../views/admin/movies/");
        exit;
    }

    // Chamo a mesma função duas vezes, mudando só o argumento da pasta
    $poster = fileUpload($_FILES['poster'], 'posters');
    $banner = fileUpload($_FILES['banner'], 'banners');

    $stmt = $conn->prepare("SELECT id FROM movies WHERE title = ?");
    $stmt->bind_param("s", $title);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0){
        $_SESSION['message'] = 'Este filme já está cadastrado!';
        header("Location: ../../views/admin/movies/");
        exit;
    } else {
        $stmt = $conn->prepare("INSERT INTO movies (title, `description`, release_yr, duration, age_rating, poster, banner, trailer_url) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiiisss", $title, $desc, $release_yr, $duration, $age_rating, $poster, $banner, $trailer);

        if($stmt->execute()){
            $_SESSION['message'] = 'Filme cadastrado com sucesso!.';
            $id = $stmt->insert_id;
            $stmt = $conn->prepare("INSERT INTO movie_genres (movie_id, genre_id) VALUES (?, ?)");

            foreach ($genres as $genre) {
                $stmt->bind_param("ii", $id, $genre);
                $stmt->execute();
            }
            
            $stmt = $conn->prepare("INSERT INTO activity_logs (action_type, entity_type, entity_id, entity_name, admin_id, admin_username) VALUES ('create', 'movie', ?, ?, ?, ?)");
            $stmt->bind_param("isis", $id, $title, $_SESSION['user_id'], $_SESSION['username']);
            $stmt->execute();
            header("Location: ../../views/admin/movies/");
            exit;
        } else {
            $_SESSION['message'] = 'Erro ao cadastrar filme.';
            header("Location: ../../views/admin/movies/");
            exit;
        }
    }
}

?>