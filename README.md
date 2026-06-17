# Orion — Plataforma de Streaming e Locação

Plataforma de streaming e locação digital de filmes (projeto acadêmico SENAI),
em PHP (MVC simples) e MySQL. Inclui o painel administrativo (gestão) e o lado
do usuário (landing, catálogo, aluguel, player, favoritos e conta).

## Como executar (ambiente local)

Roda sob Apache mais PHP (mod_php) e MySQL.

1. Banco de dados: no phpMyAdmin, crie o banco `orion` e importe o arquivo
   `database/database.sql`. Ele já cria todas as tabelas e popula o catálogo de
   exemplo (admin, gêneros, filmes e aluguéis).
2. Configuração: nada a fazer. O `app/config/config.php` detecta o ambiente pelo
   host (localhost usa o bloco local; qualquer outro host usa o de produção).
   Os dados do banco local ficam no próprio arquivo.
3. Acesse: <http://localhost/orion/> (ou `/orion/public/`).

### Login do administrador (case-sensitive)

| Usuário | Senha |
|---------|-------|
| `admin` | `Orion@2025` |

Usuários comuns de exemplo (perfil `user`, login em `/`): `joao` e `pedro`
(senha `User@2025`), `maria` (`Maria@2025`); `ana` está bloqueada para teste.
Qualquer visitante também pode criar a própria conta em Cadastre-se
(perfil sempre `user`, com login automático).

## Funcionalidades (admin)

- Login case-sensitive (mesma tela do site); perfil `admin` cai no painel.
- Dashboard com totais, top filmes alugados e atividade recente.
- CRUD de Usuários com perfil (admin/user), status e travas (não exclui nem
  rebaixa a si mesmo ou o último admin; conta com aluguéis não vira admin).
- CRUD de Filmes com gêneros, valor base, capa/banner/trailer (URLs) e destaque.
  Filme com locação não pode ser excluído (preserva o histórico).
- Catálogo na sidebar: o admin navega o catálogo, mas não aluga.

## Funcionalidades (usuário)

- Landing pública (hero com a logo sobre a parede de pôsteres) e Cadastre-se.
- Catálogo estilo streaming: hero em destaque mais linhas por gênero.
- Buscar: tela dedicada com busca por título e filtros de gênero, classificação
  e ordenação.
- Página do filme com trailer e cálculo de preço ao vivo.
- Aluguel (preço no servidor): total igual ao valor base do filme mais
  R$ 0,99 por dia. Zero dias é uma visualização única; um dia ou mais dá acesso
  ilimitado até o vencimento. Desconto de fidelidade de 30% no valor base se o
  usuário já alugou aquele filme antes.
- Para você: recomendação pelos gêneros mais frequentes da Minha Lista e dos
  aluguéis do usuário.
- Player liberado só com aluguel válido (revalidado no servidor). Meus Aluguéis
  com devolução, Minha Lista (favoritos), Conta (e-mail/senha) e Previsão de
  aluguel.

## Estrutura

```
orion/
├── public/            ponto exposto (front controller e assets)
│   ├── index.php
│   └── assets/{css,js,fonts,img}
├── app/
│   ├── bootstrap.php  sessão, autoload, helpers
│   ├── routes.php
│   ├── config/        config.php, Database.php (PDO)
│   ├── core/          Router, Controller, Model, Auth, helpers
│   ├── controllers/
│   ├── models/
│   └── views/
└── database/
    └── database.sql   schema mais dados de exemplo em um único arquivo
```

## Notas de arquitetura

- Roteamento por query-string (`index.php?url=...`): não depende de
  `mod_rewrite`, então funciona localmente e no InfinityFree.
- Segurança: PDO com prepared statements, `password_hash`/`password_verify`
  (bcrypt), CSRF em todos os POST, sessão endurecida (HttpOnly/SameSite, ID
  regenerado no login), escape de saída e guardas `defined('ORION')`.

## Deploy no InfinityFree

1. Crie o banco no painel e importe `database/database.sql` pelo phpMyAdmin.
2. Envie os arquivos por FTP ou pelo File Manager. A configuração detecta a
   produção automaticamente pelo host, então não há nada a editar.
3. Aponte o domínio para a pasta `public/`.
4. Troque a senha do admin após o primeiro login.
