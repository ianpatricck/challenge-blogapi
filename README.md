
```
 ________  ___       ________  ________          ________  ________  ___     
|\   __  \|\  \     |\   __  \|\   ____\        |\   __  \|\   __  \|\  \    
 \ \  \|\ /\ \  \    \ \  \|\  \ \  \___|        \ \  \|\  \ \  \|\  \ \  \   
  \ \   __  \ \  \    \ \  \\\  \ \  \  ___       \ \   __  \ \   ____\ \  \  
   \ \  \|\  \ \  \____\ \  \\\  \ \  \|\  \       \ \  \ \  \ \  \___|\ \  \ 
    \ \_______\ \_______\ \_______\ \_______\       \ \__\ \__\ \__\    \ \__\
     \|_______|\|_______|\|_______|\|_______|        \|__|\|__|\|__|     \|__|
```

# :fire: Blog API

Uma API Rest construída a partir de um projeto laravel que simula um blog minificado.

## 🐳 Instalando localmente

Para instalar e testar o projeto com o Laravel Sail, primeiramente faça uma cópia do arquivo ```.env.example``` para somente ```.env```.

```bash
$ cp .env.example .env 
```

Antes de iniciarmos com o Sail, instale as dependências do composer através do comando abaixo.

```bash
$ docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

Explicação do comando em: [Laravel Sail](https://laravel.com/docs/11.x/sail#installing-composer-dependencies-for-existing-projects)

Agora instale os requisitos, no caso somente o MySQL, com o comando do Sail no Artisan.

```bash
$ php artisan sail:install
```

Selecione somente o MySQL.

A seguir suba as imagens docker com o Laravel Sail a partir do binário.

```bash
$ ./vendor/bin/sail up -d
```

Em seguida rode as migrations do banco de dados para criação das tabelas.

```bash
$ ./vendor/bin/sail artisan migrate
```

Agora sua API do Blog está pronta para teste.
Abra em seu navegador no endereço ```http://localhost:8000/api/documentation``` para visualizar todos os endpoints.

⚠️ Você também pode instalar manualmente clonando o repositório e rodando ```composer install```, mas certifique-se de antecipar todas as dependências como MySQL, versão do PHP (8.4) entre outras extensões necessárias que o Laravel precisa.

