SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`      VARCHAR(50)  CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `email`         VARCHAR(150) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role`          ENUM('admin','user') NOT NULL DEFAULT 'user',
  `status`        ENUM('active','blocked') NOT NULL DEFAULT 'active',
  `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                  ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_username` (`username`),
  UNIQUE KEY `uq_users_email`    (`email`),
  KEY `idx_users_role`   (`role`),
  KEY `idx_users_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `genres`;
CREATE TABLE `genres` (
  `id`         SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(60) NOT NULL,
  `slug`       VARCHAR(60) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_genres_name` (`name`),
  UNIQUE KEY `uq_genres_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `movies`;
CREATE TABLE `movies` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`         VARCHAR(200) NOT NULL,
  `slug`          VARCHAR(220) NOT NULL,
  `synopsis`      TEXT NULL,
  `director`      VARCHAR(150) NULL,
  `cast_list`     TEXT NULL,
  `release_year`  SMALLINT UNSIGNED NULL,
  `duration_min`  SMALLINT UNSIGNED NULL,
  `age_rating`    ENUM('L','10','12','14','16','18') NOT NULL DEFAULT 'L',
  `poster_url`    VARCHAR(500) NULL,
  `backdrop_url`  VARCHAR(500) NULL,
  `trailer_url`   VARCHAR(500) NULL,
  `base_price`    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `stock`         SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  `available`     TINYINT(1) NOT NULL DEFAULT 1,
  `featured`      TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                  ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_movies_slug` (`slug`),
  KEY `idx_movies_title`     (`title`),
  KEY `idx_movies_available` (`available`),
  KEY `idx_movies_featured`  (`featured`),
  KEY `idx_movies_year`      (`release_year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `movie_genres`;
CREATE TABLE `movie_genres` (
  `movie_id` INT UNSIGNED NOT NULL,
  `genre_id` SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (`movie_id`, `genre_id`),
  KEY `idx_mg_genre` (`genre_id`),
  CONSTRAINT `fk_mg_movie` FOREIGN KEY (`movie_id`)
      REFERENCES `movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mg_genre` FOREIGN KEY (`genre_id`)
      REFERENCES `genres` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `rentals`;
CREATE TABLE `rentals` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED NOT NULL,
  `movie_id`    INT UNSIGNED NOT NULL,
  `rental_date` DATE NOT NULL,
  `days`        SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  `view_mode`   ENUM('single','period') NOT NULL DEFAULT 'period',
  `views_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `due_date`    DATE NOT NULL,
  `return_date` DATE NULL,
  `unit_price`  DECIMAL(10,2) NOT NULL,
  `base_price`  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_price` DECIMAL(10,2) NOT NULL,
  `status`      ENUM('active','returned','overdue','cancelled')
                NOT NULL DEFAULT 'active',
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rentals_user`   (`user_id`),
  KEY `idx_rentals_movie`  (`movie_id`),
  KEY `idx_rentals_status` (`status`),
  KEY `idx_rentals_date`   (`rental_date`),
  CONSTRAINT `fk_rentals_user` FOREIGN KEY (`user_id`)
      REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rentals_movie` FOREIGN KEY (`movie_id`)
      REFERENCES `movies` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `favorites`;
CREATE TABLE `favorites` (
  `user_id`    INT UNSIGNED NOT NULL,
  `movie_id`   INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `movie_id`),
  KEY `idx_fav_movie` (`movie_id`),
  CONSTRAINT `fk_fav_user` FOREIGN KEY (`user_id`)
      REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_fav_movie` FOREIGN KEY (`movie_id`)
      REFERENCES `movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED NULL,
  `action`      VARCHAR(60) NOT NULL,
  `entity_type` VARCHAR(40) NULL,
  `entity_id`   INT UNSIGNED NULL,
  `description` VARCHAR(255) NULL,
  `ip_address`  VARCHAR(45) NULL,
  `user_agent`  VARCHAR(255) NULL,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_log_user`   (`user_id`),
  KEY `idx_log_action` (`action`),
  KEY `idx_log_date`   (`created_at`),
  CONSTRAINT `fk_log_user` FOREIGN KEY (`user_id`)
      REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO `users` (`username`, `email`, `password_hash`, `role`, `status`)
VALUES
  ('admin', 'admin@orion.local',
   '$2y$12$InbAYU10d7kYuKdsv5E1x.AwDIENP4wheoJSk/wuoR72gLCI4ZDIW',
   'admin', 'active');

INSERT INTO `genres` (`name`, `slug`) VALUES
  ('Ação',          'acao'),
  ('Aventura',      'aventura'),
  ('Comédia',       'comedia'),
  ('Drama',         'drama'),
  ('Ficção Científica', 'ficcao-cientifica'),
  ('Terror',        'terror'),
  ('Suspense',      'suspense'),
  ('Romance',       'romance'),
  ('Animação',      'animacao'),
  ('Documentário',  'documentario');

INSERT INTO `users` (`username`, `email`, `password_hash`, `role`, `status`) VALUES
  ('joao',   'joao@orion.local',   '$2y$12$d9Wh5k0/YeN6dzE/2pk7J.VQmYnluY8semX2xmAxwHzxVuom0ydLG', 'user', 'active'),
  ('maria',  'maria@orion.local',  '$2y$12$FHg38.s3Q936d.TETL3kmO.PBQJshrPFunjydNo3ZhITgHKxGO1PK', 'user', 'active'),
  ('pedro',  'pedro@orion.local',  '$2y$12$d9Wh5k0/YeN6dzE/2pk7J.VQmYnluY8semX2xmAxwHzxVuom0ydLG', 'user', 'active'),
  ('ana',    'ana@orion.local',    '$2y$12$d9Wh5k0/YeN6dzE/2pk7J.VQmYnluY8semX2xmAxwHzxVuom0ydLG', 'user', 'blocked');

INSERT INTO `movies`
  (`title`, `slug`, `synopsis`, `director`, `cast_list`, `release_year`, `duration_min`,
   `age_rating`, `poster_url`, `backdrop_url`, `trailer_url`, `base_price`, `stock`, `available`, `featured`)
VALUES
  ('Interestelar', 'interestelar',
   'Um grupo de exploradores usa um buraco de minhoca recém-descoberto para superar as limitações da viagem espacial humana.',
   'Christopher Nolan', 'Matthew McConaughey, Anne Hathaway', 2014, 169, '10',
   'https://upload.wikimedia.org/wikipedia/en/b/bc/Interstellar_film_poster.jpg',
   'https://image.tmdb.org/t/p/original/xJHokMbljvjADYdit5fK5VQsXEG.jpg',
   'https://www.youtube.com/watch?v=zSWdZVtXT7E', 7.90, 5, 1, 1),

  ('A Origem', 'a-origem',
   'Um ladrão que rouba segredos corporativos por meio da tecnologia de compartilhamento de sonhos.',
   'Christopher Nolan', 'Leonardo DiCaprio, Joseph Gordon-Levitt', 2010, 148, '12',
   'https://image.tmdb.org/t/p/w500/9gk7adHYeDvHkCSEqAvQNLV5Uge.jpg',
   'https://image.tmdb.org/t/p/original/s3TBrRGB1iav7gFOCNx3H31MoES.jpg',
   'https://www.youtube.com/watch?v=YoHD9XEInc0', 6.90, 4, 1, 0),

  ('Matrix', 'matrix',
   'Um hacker descobre que a realidade é uma simulação e se junte à rebelião contra as máquinas.',
   'Lana e Lilly Wachowski', 'Keanu Reeves, Laurence Fishburne', 1999, 136, '14',
   'https://image.tmdb.org/t/p/w500/f89U3ADr1oiB1s9GkdPOEpXUk5H.jpg',
   'https://image.tmdb.org/t/p/original/icmmSD4vTTDKOq2vvdulafOGw93.jpg',
   'https://www.youtube.com/watch?v=vKQi3bBA1y8', 5.90, 6, 1, 1),

  ('O Senhor dos Anéis: A Sociedade do Anel', 'senhor-dos-aneis-sociedade',
   'Um hobbit recebe a missão de destruir um anel poderoso e salvar a Terra-média.',
   'Peter Jackson', 'Elijah Wood, Ian McKellen', 2001, 178, '12',
   'https://image.tmdb.org/t/p/w500/6oom5QYQ2yQTMJIbnvbkBL9cHo6.jpg',
   'https://image.tmdb.org/t/p/original/vRQnzOn4HjIMX4LBq9nHhFXbsSu.jpg',
   'https://www.youtube.com/watch?v=V75dMMIW2B4', 8.90, 3, 1, 0),

  ('Pulp Fiction', 'pulp-fiction',
   'As vidas de dois assassinos da máfia, um boxeador e um casal de assaltantes se entrelaçam.',
   'Quentin Tarantino', 'John Travolta, Uma Thurman', 1994, 154, '18',
   'https://image.tmdb.org/t/p/w500/d5iIlFn5s0ImszYzBPb8JPIfbXD.jpg',
   'https://image.tmdb.org/t/p/original/suaEOtk1N1sgg2MTM7oZd2cfVp3.jpg',
   'https://www.youtube.com/watch?v=s7EdQ4FqbhY', 5.50, 4, 1, 0),

  ('Toy Story', 'toy-story',
   'Os brinquedos de um menino ganham vida quando os humanos não estão por perto.',
   'John Lasseter', 'Tom Hanks, Tim Allen', 1995, 81, 'L',
   'https://image.tmdb.org/t/p/w500/uXDfjJbdP4ijW5hWSBrPrlKpxab.jpg',
   'https://image.tmdb.org/t/p/w1280/3Rfvhy1Nl6sSGJwyjb0QiZzZYlB.jpg',
   'https://www.youtube.com/watch?v=v-PjgYDrg70', 4.90, 8, 1, 0),

  ('Coringa', 'coringa',
   'Arthur Fleck, um comediante fracassado, mergulha na loucura e se torna um ícone do caos.',
   'Todd Phillips', 'Joaquin Phoenix, Robert De Niro', 2019, 122, '16',
   'https://upload.wikimedia.org/wikipedia/en/e/e1/Joker_%282019_film%29_poster.jpg',
   'https://image.tmdb.org/t/p/original/n6bUvigpRFqSwmPp1m2YADdbRBc.jpg',
   'https://www.youtube.com/watch?v=zAGVQLHvwOY', 6.50, 5, 1, 1),

  ('Parasita', 'parasita',
   'Uma família pobre se infiltra na vida de uma família rica com consequências inesperadas.',
   'Bong Joon-ho', 'Song Kang-ho, Lee Sun-kyun', 2019, 132, '16',
   'https://upload.wikimedia.org/wikipedia/en/5/53/Parasite_%282019_film%29.png',
   'https://image.tmdb.org/t/p/original/TU9NIjwzjoKPwQHoHshkFcQUCG.jpg',
   'https://www.youtube.com/watch?v=isOGD_7hNIY', 6.90, 2, 1, 0),

  ('Vingadores: Ultimato', 'vingadores-ultimato',
   'Os heróis remanescentes se unem para reverter as ações de Thanos e restaurar o universo.',
   'Anthony e Joe Russo', 'Robert Downey Jr., Chris Evans', 2019, 181, '12',
   'https://image.tmdb.org/t/p/w500/ulzhLuWrPK07P1YkdWQLZnQh1JL.jpg',
   'https://image.tmdb.org/t/p/original/7RyHsO4yDXtBv1zUU3mTpHeQ0d5.jpg',
   'https://www.youtube.com/watch?v=TcMBFSGVi1c', 7.50, 6, 1, 0),

  ('Cidade de Deus', 'cidade-de-deus',
   'A história do crime organizado em uma favela do Rio de Janeiro ao longo de três décadas.',
   'Fernando Meirelles', 'Alexandre Rodrigues, Leandro Firmino', 2002, 130, '18',
   'https://upload.wikimedia.org/wikipedia/en/1/10/CidadedeDeus.jpg',
   'https://image.tmdb.org/t/p/w1280/uvitbjFU4JqvMwIkMWHp69bmUzG.jpg',
   'https://www.youtube.com/watch?v=RfnGQetbX-U', 5.90, 3, 0, 0);

INSERT INTO `movie_genres` (`movie_id`, `genre_id`) VALUES
  (1,5),(1,2),(1,4),
  (2,5),(2,1),(2,7),
  (3,5),(3,1),
  (4,2),(4,5),
  (5,4),(5,7),
  (6,9),(6,2),(6,3),
  (7,4),(7,7),
  (8,4),(8,7),
  (9,1),(9,2),
  (10,4);

INSERT INTO `rentals`
  (`user_id`, `movie_id`, `rental_date`, `days`, `due_date`, `return_date`, `unit_price`, `total_price`, `status`)
VALUES
  (2, 3, DATE_SUB(CURDATE(), INTERVAL 10 DAY), 3, DATE_SUB(CURDATE(), INTERVAL 7 DAY), DATE_SUB(CURDATE(), INTERVAL 8 DAY), 5.90, 17.70, 'returned'),
  (3, 3, DATE_SUB(CURDATE(), INTERVAL 5 DAY),  2, DATE_SUB(CURDATE(), INTERVAL 3 DAY), NULL, 5.90, 11.80, 'active'),
  (4, 1, DATE_SUB(CURDATE(), INTERVAL 2 DAY),  4, DATE_ADD(CURDATE(), INTERVAL 2 DAY), NULL, 7.90, 31.60, 'active'),
  (2, 1, DATE_SUB(CURDATE(), INTERVAL 20 DAY), 1, DATE_SUB(CURDATE(), INTERVAL 19 DAY), DATE_SUB(CURDATE(), INTERVAL 19 DAY), 7.90, 7.90, 'returned'),
  (3, 7, DATE_SUB(CURDATE(), INTERVAL 1 DAY),  3, DATE_ADD(CURDATE(), INTERVAL 2 DAY), NULL, 6.50, 19.50, 'active'),
  (4, 7, DATE_SUB(CURDATE(), INTERVAL 15 DAY), 2, DATE_SUB(CURDATE(), INTERVAL 13 DAY), DATE_SUB(CURDATE(), INTERVAL 13 DAY), 6.50, 13.00, 'returned'),
  (2, 5, DATE_SUB(CURDATE(), INTERVAL 12 DAY), 5, DATE_SUB(CURDATE(), INTERVAL 7 DAY), DATE_SUB(CURDATE(), INTERVAL 6 DAY), 5.50, 27.50, 'returned'),
  (3, 9, DATE_SUB(CURDATE(), INTERVAL 8 DAY),  3, DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_SUB(CURDATE(), INTERVAL 4 DAY), 7.50, 22.50, 'returned');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('O Poderoso Chefão','o-poderoso-chefao','A saga da família Corleone e a sucessão no comando de um império do crime.','Francis Ford Coppola','Marlon Brando, Al Pacino',1972,175,'16','https://upload.wikimedia.org/wikipedia/en/1/1c/Godfather_ver1.jpg','https://image.tmdb.org/t/p/w1280/tSPT36ZKlP2WVHJLM4cQPLSzv3b.jpg','https://www.youtube.com/watch?v=sY1S34973zA',6.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='o-poderoso-chefao'), id FROM genres WHERE slug IN ('drama','suspense');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Um Sonho de Liberdade','um-sonho-de-liberdade','Condenado injustamente, um bancário mantém a esperança e a amizade na prisão de Shawshank.','Frank Darabont','Tim Robbins, Morgan Freeman',1994,142,'16','https://upload.wikimedia.org/wikipedia/en/8/81/ShawshankRedemptionMoviePoster.jpg','https://image.tmdb.org/t/p/w1280/zfbjgQE1uSd9wiPTX4VzsLi0rGG.jpg','https://www.youtube.com/watch?v=6hB3S9bIaco',5.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='um-sonho-de-liberdade'), id FROM genres WHERE slug IN ('drama');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Forrest Gump: O Contador de Histórias','forrest-gump','Um homem simples atravessa décadas da história americana movido pelo amor de sua vida.','Robert Zemeckis','Tom Hanks, Robin Wright',1994,142,'12','https://upload.wikimedia.org/wikipedia/en/6/67/Forrest_Gump_poster.jpg','https://image.tmdb.org/t/p/w1280/66Kn4XWhkuPkJxOJyPEx4U2CUfN.jpg','https://www.youtube.com/watch?v=bLvqoHBptjg',5.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='forrest-gump'), id FROM genres WHERE slug IN ('comedia','drama','romance');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Clube da Luta','clube-da-luta','Um insone e um vendedor de sabão criam um clube secreto de luta que sai do controle.','David Fincher','Brad Pitt, Edward Norton',1999,139,'18','https://upload.wikimedia.org/wikipedia/en/f/fc/Fight_Club_poster.jpg','https://image.tmdb.org/t/p/w1280/xRyINp9KfMLVjRiO5nCsoRDdvvF.jpg','https://www.youtube.com/watch?v=qtRKdVHc-cE',5.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='clube-da-luta'), id FROM genres WHERE slug IN ('drama','suspense');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('O Cavaleiro das Trevas','o-cavaleiro-das-trevas','Batman enfrenta o Coringa, um criminoso que mergulha Gotham no caos.','Christopher Nolan','Christian Bale, Heath Ledger',2008,152,'12','https://upload.wikimedia.org/wikipedia/en/1/1c/The_Dark_Knight_%282008_film%29.jpg','https://image.tmdb.org/t/p/w1280/cfT29Im5VDvjE0RpyKOSdCKZal7.jpg','https://www.youtube.com/watch?v=EXeTwQWrcwY',6.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='o-cavaleiro-das-trevas'), id FROM genres WHERE slug IN ('acao','drama','suspense');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Gladiador','gladiador','Um general romano traído busca vingança como gladiador na arena.','Ridley Scott','Russell Crowe, Joaquin Phoenix',2000,155,'14','https://upload.wikimedia.org/wikipedia/en/f/fb/Gladiator_%282000_film_poster%29.png','https://image.tmdb.org/t/p/w1280/jhk6D8pim3yaByu1801kMoxXFaX.jpg','https://www.youtube.com/watch?v=owK1qxDselE',6.50,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='gladiador'), id FROM genres WHERE slug IN ('acao','aventura','drama');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Titanic','titanic','Um romance proibido floresce a bordo do transatlântico em sua viagem fatídica.','James Cameron','Leonardo DiCaprio, Kate Winslet',1997,195,'12','https://upload.wikimedia.org/wikipedia/en/1/18/Titanic_%281997_film%29_poster.png','https://image.tmdb.org/t/p/w1280/xnHVX37XZEp33hhCbYlQFq7ux1J.jpg','https://www.youtube.com/watch?v=2e-eXJ6HgkQ',6.50,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='titanic'), id FROM genres WHERE slug IN ('drama','romance');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Avatar','avatar','Em Pandora, um ex-fuzileiro se vê dividido entre seguir ordens e proteger um novo mundo.','James Cameron','Sam Worthington, Zoe Saldaña',2009,162,'12','https://upload.wikimedia.org/wikipedia/en/d/d6/Avatar_%282009_film%29_poster.jpg','https://image.tmdb.org/t/p/w1280/vL5LR6WdxWPjLPFRLe133jXWsh5.jpg','https://www.youtube.com/watch?v=5PSNL1qE6VY',7.50,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='avatar'), id FROM genres WHERE slug IN ('acao','aventura','ficcao-cientifica');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Os Vingadores','os-vingadores','Os maiores heróis da Terra se unem para deter a invasão de Loki.','Joss Whedon','Robert Downey Jr., Chris Evans',2012,143,'12','https://upload.wikimedia.org/wikipedia/en/8/8a/The_Avengers_%282012_film%29_poster.jpg','https://image.tmdb.org/t/p/w1280/9BBTo63ANSmhC4e6r62OJFuK2GL.jpg','https://www.youtube.com/watch?v=eOrNdBpGMv8',6.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='os-vingadores'), id FROM genres WHERE slug IN ('acao','aventura','ficcao-cientifica');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Vingadores: Guerra Infinita','vingadores-guerra-infinita','Os heróis se unem para impedir Thanos de coletar as Joias do Infinito.','Anthony e Joe Russo','Robert Downey Jr., Josh Brolin',2018,149,'12','https://upload.wikimedia.org/wikipedia/en/4/4d/Avengers_Infinity_War_poster.jpg','https://image.tmdb.org/t/p/w1280/mDfJG3LC3Dqb67AZ52x3Z0jU0uB.jpg','https://www.youtube.com/watch?v=6ZfuNTqbHE8',7.50,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='vingadores-guerra-infinita'), id FROM genres WHERE slug IN ('acao','aventura','ficcao-cientifica');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Batman Begins','batman-begins','A origem de Bruce Wayne e seu surgimento como o protetor de Gotham.','Christopher Nolan','Christian Bale, Michael Caine',2005,140,'12','https://upload.wikimedia.org/wikipedia/en/a/af/Batman_Begins_Poster.jpg','https://image.tmdb.org/t/p/w1280/9IIBboV7MCT0bTxzXHmWK1Hq558.jpg','https://www.youtube.com/watch?v=neY2xVmOfUM',5.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='batman-begins'), id FROM genres WHERE slug IN ('acao','aventura');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Bastardos Inglórios','bastardos-ingloriosos','Um grupo de soldados judeus espalha o terror entre os nazistas na França ocupada.','Quentin Tarantino','Brad Pitt, Christoph Waltz',2009,153,'18','https://upload.wikimedia.org/wikipedia/en/c/c3/Inglourious_Basterds_poster.jpg','https://image.tmdb.org/t/p/w1280/hwNtEmmugU5Yd7hpfprNWI0DGIn.jpg','https://www.youtube.com/watch?v=KnrRy6kSFF0',6.50,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='bastardos-ingloriosos'), id FROM genres WHERE slug IN ('acao','drama','suspense');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Django Livre','django-livre','Um escravo liberto une forças com um caçador de recompensas para resgatar sua esposa.','Quentin Tarantino','Jamie Foxx, Christoph Waltz',2012,165,'18','https://upload.wikimedia.org/wikipedia/en/8/8b/Django_Unchained_Poster.jpg','https://image.tmdb.org/t/p/w1280/2oZklIzUbvZXXzIFzv7Hi68d6xf.jpg','https://www.youtube.com/watch?v=0fUCuvNlOCg',6.50,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='django-livre'), id FROM genres WHERE slug IN ('acao','drama');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('O Lobo de Wall Street','o-lobo-de-wall-street','A ascensão e queda de um corretor que enriquece com fraudes em Wall Street.','Martin Scorsese','Leonardo DiCaprio, Jonah Hill',2013,180,'18','https://upload.wikimedia.org/wikipedia/en/d/d8/The_Wolf_of_Wall_Street_%282013%29.png','https://image.tmdb.org/t/p/w1280/7Nwnmyzrtd0FkcRyPqmdzTPppQa.jpg','https://www.youtube.com/watch?v=iszwuX1AK6A',6.50,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='o-lobo-de-wall-street'), id FROM genres WHERE slug IN ('comedia','drama');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Os Infiltrados','os-infiltrados','Um policial infiltrado e um espião da máfia tentam se desmascarar mutuamente.','Martin Scorsese','Leonardo DiCaprio, Matt Damon',2006,151,'16','https://upload.wikimedia.org/wikipedia/en/5/50/Departed234.jpg','https://image.tmdb.org/t/p/w1280/6WRrGYalXXveItfpnipYdayFkQB.jpg','https://www.youtube.com/watch?v=auYbpnEwBBg',6.50,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='os-infiltrados'), id FROM genres WHERE slug IN ('drama','suspense');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('A Lista de Schindler','a-lista-de-schindler','Um empresário salva centenas de judeus durante o Holocausto.','Steven Spielberg','Liam Neeson, Ralph Fiennes',1993,195,'14','https://upload.wikimedia.org/wikipedia/en/3/38/Schindler%27s_List_movie.jpg','https://image.tmdb.org/t/p/w1280/zb6fM1CX41D9rF9hdgclu0peUmy.jpg','https://www.youtube.com/watch?v=gG22XNhtnoY',5.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='a-lista-de-schindler'), id FROM genres WHERE slug IN ('drama');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Jurassic Park','jurassic-park','Um parque com dinossauros clonados vira um pesadelo quando a segurança falha.','Steven Spielberg','Sam Neill, Laura Dern',1993,127,'12','https://upload.wikimedia.org/wikipedia/en/e/e7/Jurassic_Park_poster.jpg','https://image.tmdb.org/t/p/w1280/o7LzVmlOSYc3EspyVMC9bsTTARc.jpg','https://www.youtube.com/watch?v=lc0UehYemQA',5.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='jurassic-park'), id FROM genres WHERE slug IN ('aventura','ficcao-cientifica');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('O Resgate do Soldado Ryan','o-resgate-do-soldado-ryan','Após o Dia D, um pelotão parte em missão para resgatar um único soldado.','Steven Spielberg','Tom Hanks, Matt Damon',1998,169,'16','https://upload.wikimedia.org/wikipedia/en/a/ac/Saving_Private_Ryan_poster.jpg','https://image.tmdb.org/t/p/w1280/bdD39MpSVhKjxarTxLSfX6baoMP.jpg','https://www.youtube.com/watch?v=zwhP5b4tD6g',5.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='o-resgate-do-soldado-ryan'), id FROM genres WHERE slug IN ('acao','drama');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('De Volta para o Futuro','de-volta-para-o-futuro','Um adolescente viaja aos anos 1950 em um DeLorean e precisa garantir seu próprio futuro.','Robert Zemeckis','Michael J. Fox, Christopher Lloyd',1985,116,'10','https://upload.wikimedia.org/wikipedia/en/d/d2/Back_to_the_Future.jpg','https://image.tmdb.org/t/p/w1280/5bzPWQ2dFUl2aZKkp7ILJVVkRed.jpg','https://www.youtube.com/watch?v=qvsgGtivCgs',4.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='de-volta-para-o-futuro'), id FROM genres WHERE slug IN ('aventura','comedia','ficcao-cientifica');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Alien, o Oitavo Passageiro','alien-o-oitavo-passageiro','A tripulação de uma nave é caçada por uma criatura mortal no espaço.','Ridley Scott','Sigourney Weaver, Tom Skerritt',1979,117,'16','https://upload.wikimedia.org/wikipedia/en/c/c3/Alien_movie_poster.jpg','https://image.tmdb.org/t/p/w1280/AmR3JG1VQVxU8TfAvljUhfSFUOx.jpg','https://www.youtube.com/watch?v=LjLamj-b0I8',5.50,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='alien-o-oitavo-passageiro'), id FROM genres WHERE slug IN ('ficcao-cientifica','terror','suspense');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('O Iluminado','o-iluminado','Um escritor enlouquece ao cuidar de um hotel isolado durante o inverno.','Stanley Kubrick','Jack Nicholson, Shelley Duvall',1980,146,'16','https://upload.wikimedia.org/wikipedia/en/1/1d/The_Shining_%281980%29_U.K._release_poster_-_The_tide_of_terror_that_swept_America_IS_HERE.jpg','https://image.tmdb.org/t/p/w1280/mmd1HnuvAzFc4iuVJcnBrhDNEKr.jpg','https://www.youtube.com/watch?v=S014oGZiSdI',5.50,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='o-iluminado'), id FROM genres WHERE slug IN ('terror','suspense');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('O Exorcista','o-exorcista','Dois padres tentam salvar uma menina possuída por uma entidade demoníaca.','William Friedkin','Ellen Burstyn, Linda Blair',1973,122,'18','https://upload.wikimedia.org/wikipedia/en/7/7b/Exorcist_ver2.jpg','https://image.tmdb.org/t/p/w1280/xcjJ5khg2yzOa282mza39Lbrm7j.jpg','https://www.youtube.com/watch?v=YDGw1MTEe9k',5.50,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='o-exorcista'), id FROM genres WHERE slug IN ('terror');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Corra!','corra','Um jovem negro descobre um segredo aterrorizante ao conhecer a família da namorada.','Jordan Peele','Daniel Kaluuya, Allison Williams',2017,104,'16','https://upload.wikimedia.org/wikipedia/en/a/a3/Get_Out_poster.png','https://image.tmdb.org/t/p/w1280/o8dPH0ZSIyyViP6rjRX1djwCUwI.jpg','https://www.youtube.com/watch?v=DzfpyUB60YY',5.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='corra'), id FROM genres WHERE slug IN ('terror','suspense');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Hereditário','hereditario','Após a morte da avó, uma família é assombrada por um legado sinistro.','Ari Aster','Toni Collette, Alex Wolff',2018,127,'16','https://upload.wikimedia.org/wikipedia/commons/thumb/f/f0/Hybridogenesis_in_water_frogs_gametes.svg/500px-Hybridogenesis_in_water_frogs_gametes.svg.png','https://image.tmdb.org/t/p/w1280/gJbTXKNTL6O7r7PzF6ZRkJGBlPp.jpg','https://www.youtube.com/watch?v=V6wWKNij_1M',5.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='hereditario'), id FROM genres WHERE slug IN ('drama','terror','suspense');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('It: A Coisa','it-a-coisa','Crianças enfrentam um palhaço demoníaco que aterroriza sua cidade.','Andy Muschietti','Bill Skarsgård, Jaeden Martell',2017,135,'16','https://upload.wikimedia.org/wikipedia/en/5/5a/It_%282017%29_poster.jpg','https://image.tmdb.org/t/p/w1280/qVGpxnjrGlHaSTCqTQI6viBDSfp.jpg','https://www.youtube.com/watch?v=FnCdOQsX5kc',5.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='it-a-coisa'), id FROM genres WHERE slug IN ('terror','suspense');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Seven: Os Sete Crimes Capitais','seven-os-sete-crimes-capitais','Dois detetives caçam um assassino que mata inspirado nos sete pecados capitais.','David Fincher','Brad Pitt, Morgan Freeman',1995,127,'18','https://upload.wikimedia.org/wikipedia/en/6/68/Seven_%28movie%29_poster.jpg','https://image.tmdb.org/t/p/w1280/i5H7zusQGsysGQ8i6P361Vnr0n2.jpg','https://www.youtube.com/watch?v=znmZoVkCjpI',5.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='seven-os-sete-crimes-capitais'), id FROM genres WHERE slug IN ('drama','terror','suspense');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Garota Exemplar','garota-exemplar','O desaparecimento de uma mulher transforma o marido em principal suspeito.','David Fincher','Ben Affleck, Rosamund Pike',2014,149,'16','https://upload.wikimedia.org/wikipedia/en/0/05/Gone_Girl_Poster.jpg','https://image.tmdb.org/t/p/w1280/iWak7wT0j6ycCc8lKr4NBz9c7n5.jpg','https://www.youtube.com/watch?v=2-_-1nJf8Vg',5.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='garota-exemplar'), id FROM genres WHERE slug IN ('drama','suspense');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('À Espera de um Milagre','a-espera-de-um-milagre','Um carcereiro descobre dons sobrenaturais em um condenado gentil no corredor da morte.','Frank Darabont','Tom Hanks, Michael Clarke Duncan',1999,189,'16','https://upload.wikimedia.org/wikipedia/en/e/e2/The_Green_Mile_%28movie_poster%29.jpg','https://image.tmdb.org/t/p/w1280/b6HWTOxn1xevvyHU2K9ICvaRU6g.jpg','https://www.youtube.com/watch?v=Ki4haFrqSrw',5.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='a-espera-de-um-milagre'), id FROM genres WHERE slug IN ('drama','suspense');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Whiplash: Em Busca da Perfeição','whiplash','Um jovem baterista é levado ao limite por um maestro implacável.','Damien Chazelle','Miles Teller, J.K. Simmons',2014,106,'14','https://upload.wikimedia.org/wikipedia/en/0/01/Whiplash_poster.jpg','https://image.tmdb.org/t/p/w1280/wbQa0EnWUyRzQ5d1pHLNRlmsCUP.jpg','https://www.youtube.com/watch?v=7d_jQycdQGo',5.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='whiplash'), id FROM genres WHERE slug IN ('drama','suspense');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('La La Land: Cantando Estações','la-la-land','Um pianista e uma aspirante a atriz se apaixonam enquanto perseguem seus sonhos.','Damien Chazelle','Ryan Gosling, Emma Stone',2016,128,'10','https://upload.wikimedia.org/wikipedia/en/a/ab/La_La_Land_%28film%29.png','https://image.tmdb.org/t/p/w1280/nlPCdZlHtRNcF6C9hzUH4ebmV1w.jpg','https://www.youtube.com/watch?v=0pdqf4P9MB8',5.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='la-la-land'), id FROM genres WHERE slug IN ('comedia','drama','romance');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('O Grande Hotel Budapeste','o-grande-hotel-budapeste','As aventuras de um concierge lendário e seu protegido em um hotel europeu.','Wes Anderson','Ralph Fiennes, Tony Revolori',2014,99,'14','https://upload.wikimedia.org/wikipedia/en/1/1c/The_Grand_Budapest_Hotel.png','https://image.tmdb.org/t/p/w1280/9udCLTxTFl28RxnK8Q05E154ZGa.jpg','https://www.youtube.com/watch?v=1Fg5iWmQjwk',5.50,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='o-grande-hotel-budapeste'), id FROM genres WHERE slug IN ('aventura','comedia','drama');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Mad Max: Estrada da Fúria','mad-max-estrada-da-furia','Em um deserto pós-apocalíptico, fugitivos enfrentam um tirano em perseguições alucinantes.','George Miller','Tom Hardy, Charlize Theron',2015,120,'16','https://upload.wikimedia.org/wikipedia/en/6/6e/Mad_Max_Fury_Road.jpg','https://image.tmdb.org/t/p/w1280/uT895WNwm0aIJRtGizcQhrejWUo.jpg','https://www.youtube.com/watch?v=hEJnMQG9ev8',6.50,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='mad-max-estrada-da-furia'), id FROM genres WHERE slug IN ('acao','aventura','ficcao-cientifica');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('John Wick: De Volta ao Jogo','john-wick','Um ex-assassino retorna à ativa para vingar a morte de seu cão.','Chad Stahelski','Keanu Reeves, Michael Nyqvist',2014,101,'16','https://upload.wikimedia.org/wikipedia/en/9/98/John_Wick_TeaserPoster.jpg','https://image.tmdb.org/t/p/w1280/ff2ti5DkA9UYLzyqhQfI2kZqEuh.jpg','https://www.youtube.com/watch?v=C0BMx-qxsP4',5.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='john-wick'), id FROM genres WHERE slug IN ('acao','suspense');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Duna','duna','O jovem Paul Atreides é arrastado a uma guerra pelo controle do planeta Arrakis.','Denis Villeneuve','Timothée Chalamet, Zendaya',2021,155,'14','https://upload.wikimedia.org/wikipedia/en/8/8e/Dune_%282021_film%29.jpg','https://image.tmdb.org/t/p/w1280/qVgZu5BTx6pu4owCvVOm4zjTfOi.jpg','https://www.youtube.com/watch?v=8g18jFHCLXk',7.50,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='duna'), id FROM genres WHERE slug IN ('aventura','ficcao-cientifica');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Blade Runner 2049','blade-runner-2049','Um novo blade runner descobre um segredo capaz de mergulhar a sociedade no caos.','Denis Villeneuve','Ryan Gosling, Harrison Ford',2017,164,'16','https://upload.wikimedia.org/wikipedia/en/9/9b/Blade_Runner_2049_poster.png','https://image.tmdb.org/t/p/w1280/mVr0UiqyltcfqxbAUcLl9zWL8ah.jpg','https://www.youtube.com/watch?v=gCcx85zbxz4',6.50,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='blade-runner-2049'), id FROM genres WHERE slug IN ('drama','ficcao-cientifica','suspense');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Senhor dos Anéis: As Duas Torres','senhor-dos-aneis-duas-torres','A Sociedade se divide enquanto a guerra pela Terra-média se intensifica.','Peter Jackson','Elijah Wood, Ian McKellen',2002,179,'12','https://upload.wikimedia.org/wikipedia/en/a/a1/Lord_Rings_Two_Towers.jpg','https://image.tmdb.org/t/p/w1280/6G73mNyooWAEQTpckPSnFxFoNmc.jpg','https://www.youtube.com/watch?v=hYcw5ksV8YQ',7.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='senhor-dos-aneis-duas-torres'), id FROM genres WHERE slug IN ('acao','aventura','drama');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Senhor dos Anéis: O Retorno do Rei','senhor-dos-aneis-retorno-do-rei','Frodo chega à Montanha da Perdição enquanto a batalha final decide o destino de todos.','Peter Jackson','Elijah Wood, Viggo Mortensen',2003,201,'12','https://upload.wikimedia.org/wikipedia/en/4/48/Lord_Rings_Return_King.jpg','https://image.tmdb.org/t/p/w1280/ctiw6FZK4N36LmkjSklWEbuvlq9.jpg','https://www.youtube.com/watch?v=y2rYRu8UW8M',7.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='senhor-dos-aneis-retorno-do-rei'), id FROM genres WHERE slug IN ('acao','aventura','drama');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Harry Potter e a Pedra Filosofal','harry-potter-pedra-filosofal','Um garoto descobre que é bruxo e inicia seus estudos em Hogwarts.','Chris Columbus','Daniel Radcliffe, Emma Watson',2001,152,'10','https://upload.wikimedia.org/wikipedia/en/7/7a/Harry_Potter_and_the_Philosopher%27s_Stone_banner.jpg','https://image.tmdb.org/t/p/w1280/1XAC6RPT01UX9EQGy2JVn5c8pgy.jpg','https://www.youtube.com/watch?v=VyHV0BRtdxo',5.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='harry-potter-pedra-filosofal'), id FROM genres WHERE slug IN ('aventura');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Homem-Aranha: No Aranhaverso','homem-aranha-no-aranhaverso','Miles Morales une forças com aranhas de outras dimensões.','Bob Persichetti','Shameik Moore, Jake Johnson',2018,117,'10','https://upload.wikimedia.org/wikipedia/en/f/fa/Spider-Man_Into_the_Spider-Verse_poster.png','https://image.tmdb.org/t/p/w1280/8mnXR9rey5uQ08rZAvzojKWbDQS.jpg','https://www.youtube.com/watch?v=g4Hbz2jLxvQ',6.50,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='homem-aranha-no-aranhaverso'), id FROM genres WHERE slug IN ('acao','aventura','animacao');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('O Rei Leão','o-rei-leao','O filhote Simba precisa assumir seu lugar como rei das Terras do Reino.','Roger Allers','Matthew Broderick, James Earl Jones',1994,88,'L','https://upload.wikimedia.org/wikipedia/en/3/3d/The_Lion_King_poster.jpg','https://image.tmdb.org/t/p/w1280/q00H8EqULYSK74lgevMkhmGGLHn.jpg','https://www.youtube.com/watch?v=4sj1MT05lAA',4.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='o-rei-leao'), id FROM genres WHERE slug IN ('aventura','drama','animacao');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Procurando Nemo','procurando-nemo','Um peixe-palhaço cruza o oceano para reencontrar o filho desaparecido.','Andrew Stanton','Albert Brooks, Ellen DeGeneres',2003,100,'L','https://upload.wikimedia.org/wikipedia/en/2/29/Finding_Nemo.jpg','https://image.tmdb.org/t/p/w1280/eCynaAOgYYiw5yN5lBwz3IxqvaW.jpg','https://www.youtube.com/watch?v=wZdpNglLbt8',4.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='procurando-nemo'), id FROM genres WHERE slug IN ('aventura','comedia','animacao');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Os Incríveis','os-incriveis','Uma família de super-heróis tenta levar uma vida comum até serem convocados de volta.','Brad Bird','Craig T. Nelson, Holly Hunter',2004,115,'L','https://upload.wikimedia.org/wikipedia/en/2/27/The_Incredibles_%282004_animated_feature_film%29.jpg','https://image.tmdb.org/t/p/w1280/lxwzY9vNwjDgxWKt3zZ6zcU6rEJ.jpg','https://www.youtube.com/watch?v=-UaGUdNJdRQ',4.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='os-incriveis'), id FROM genres WHERE slug IN ('acao','aventura','animacao');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Divertida Mente','divertida-mente','As emoções de uma garota enfrentam o caos de uma mudança de cidade.','Pete Docter','Amy Poehler, Phyllis Smith',2015,95,'L','https://upload.wikimedia.org/wikipedia/en/0/0a/Inside_Out_%282015_film%29_poster.jpg','https://image.tmdb.org/t/p/w1280/o3i6AfTcWAuNvzAUV3q5lOmi6Gx.jpg','https://www.youtube.com/watch?v=yRUAzGQ3nSY',4.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='divertida-mente'), id FROM genres WHERE slug IN ('comedia','drama','animacao');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Viva: A Vida é uma Festa','viva-a-vida-e-uma-festa','Um menino viaja à Terra dos Mortos para descobrir o legado musical da família.','Lee Unkrich','Anthony Gonzalez, Gael García Bernal',2017,105,'L','https://upload.wikimedia.org/wikipedia/en/9/98/Coco_%282017_film%29_poster.jpg','https://image.tmdb.org/t/p/w1280/g7CHF8gTLGooTbP4GznIGwaqAGL.jpg','https://www.youtube.com/watch?v=Rvr68u6k5sI',4.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='viva-a-vida-e-uma-festa'), id FROM genres WHERE slug IN ('aventura','comedia','animacao');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('WALL·E','wall-e','Um robô solitário na Terra abandonada encontra o amor e uma missão para a humanidade.','Andrew Stanton','Ben Burtt, Elissa Knight',2008,98,'L','https://upload.wikimedia.org/wikipedia/en/4/4c/WALL-E_poster.jpg','https://image.tmdb.org/t/p/w1280/nYs4ZwnJBK4AgljhvzwNz7fpr3E.jpg','https://www.youtube.com/watch?v=alIq_wG9FNk',4.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='wall-e'), id FROM genres WHERE slug IN ('aventura','ficcao-cientifica','animacao');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Shrek','shrek','Um ogro mal-humorado parte em uma jornada para resgatar uma princesa.','Andrew Adamson','Mike Myers, Eddie Murphy',2001,90,'L','https://upload.wikimedia.org/wikipedia/en/7/7b/Shrek_%282001_animated_feature_film%29.jpg','https://image.tmdb.org/t/p/w1280/40Wtp7kMG6mZ4d5T1jfrd8qrvD4.jpg','https://www.youtube.com/watch?v=CwXOrWvPBPk',4.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='shrek'), id FROM genres WHERE slug IN ('aventura','comedia','animacao');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Senna','senna','A trajetória do tricampeão de Fórmula 1 Ayrton Senna, dentro e fora das pistas.','Asif Kapadia','Ayrton Senna',2010,106,'L','https://upload.wikimedia.org/wikipedia/en/8/89/Senna_film_poster.jpg','https://image.tmdb.org/t/p/w1280/vtXCGAkCx5o2p0jn3A1QmdR5u5i.jpg','https://www.youtube.com/watch?v=sfosF-ZAbR4',4.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='senna'), id FROM genres WHERE slug IN ('documentario');

INSERT INTO `movies` (`title`,`slug`,`synopsis`,`director`,`cast_list`,`release_year`,`duration_min`,`age_rating`,`poster_url`,`backdrop_url`,`trailer_url`,`base_price`,`available`,`featured`) VALUES
  ('Free Solo','free-solo','O alpinista Alex Honnold tenta escalar o El Capitan sem cordas.','Elizabeth Chai Vasarhelyi','Alex Honnold',2018,100,'12','https://upload.wikimedia.org/wikipedia/en/9/9c/Free_Solo.png','https://image.tmdb.org/t/p/w1280/z2uuQasY4gQJ8VDAFki746JWeQJ.jpg','https://www.youtube.com/watch?v=urRVZ4SW7WU',4.90,1,0);
INSERT INTO `movie_genres` (`movie_id`,`genre_id`)
  SELECT (SELECT id FROM movies WHERE slug='free-solo'), id FROM genres WHERE slug IN ('aventura','documentario');
