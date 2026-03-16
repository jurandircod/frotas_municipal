Sumário

Requisitos

Instalação rápida (local)

Banco de dados

Rodando o app

Compilando assets (Tailwind / JS)

Views (Blade) — Convenções e exemplos

Charts (Chart.js) — integração

Sidebar / Layout / Modais — como funcionam

Controllers & Routes importantes

Models & Relacionamentos essenciais

Deploy rápido / Produção

Solução de problemas comuns

Comandos úteis

To-do / melhorias recomendadas

Requisitos

PHP >= 8.0 (recomendado 8.1+)

Composer

MySQL (ou MariaDB) compatível com collation utf8mb4

Node.js + npm (para compilar assets se você usar Tailwind via npm)

Extensões PHP comuns: pdo_mysql, mbstring, tokenizer, openssl, json, ctype, xml

Instalação rápida (local)

Clone o repositório:

git clone <repo-url> projeto-frota
cd projeto-frota

Instale dependências PHP:

composer install

Copie o .env:

cp .env.example .env

Edite .env e configure as variáveis DB_* (ver exemplo abaixo).

Gere a chave do aplicativo:

php artisan key:generate
Banco de dados

Você já tem o script SQL do modelo (o que você me enviou). Duas opções:

A — Importar o script SQL (MySQL Workbench / CLI):

mysql -u root -p
CREATE DATABASE frotas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit
mysql -u root -p frotas < path/to/sql-script.sql

B — Usar migrations do Laravel (se preferir executar migrations):

php artisan migrate
# opcional: seeders se existirem
php artisan db:seed

Exemplo .env (apenas DB):

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=frotas
DB_USERNAME=root
DB_PASSWORD=senha

Se você importou via SQL, verifique se os dados carregados estão corretos (veículos, tipos, usuários).

Rodando o app (ambiente dev)

Servidor embutido:

php artisan serve
# abre http://127.0.0.1:8000

Se usar Laravel Sail (Docker) — exemplo:

./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
Compilando assets (Tailwind / JS)

Temos duas abordagens: CDN (rápido) ou Tailwind via npm (recomendado em produção).

CDN (usado nas views que te passei)

As views já usam Tailwind via CDN e Chart.js via CDN. Sem build necessário.

npm (opcional, recomendado)

Instale:

npm install
# se ainda não houver package.json, adicione Tailwind/Chart.js
npm install tailwindcss postcss autoprefixer chart.js --save-dev
npx tailwindcss init

Ajuste resources/css/app.css e resources/js/app.js e rode:

npm run dev   # ou npm run build para produção

Se usar Vite: configure vite.config.js e scripts em package.json.

Views (Blade) — Convenções e exemplos

Estrutura base:

resources/
  views/
    layouts/
      app.blade.php     # layout principal (header, sidebar, footer)
    dashboard.blade.php
    veiculos/
      index.blade.php
      create.blade.php
      edit.blade.php
    movimentacoes/
      index.blade.php
      create.blade.php
      edit.blade.php
    motoristas/
      index.blade.php
Convenções que usamos

@extends('layouts.app') — layout principal.

@section('title') e @section('page_header') — preenchidos por cada view.

Scripts JS específicos da view: usar @push('scripts') no blade filho e @stack('scripts') no layouts.app (assim os scripts aparecem no fim da página).

CSS inline mínimo — preferimos classes Tailwind.

Exemplos práticos

Formulário de criação (exemplo de botão submit móvel fixo):

<form id="movForm" method="POST" action="{{ route('movimentacao.store') }}">
  @csrf
  <!-- campos -->
</form>

<!-- botão fixo (mobile) -->
<div class="fixed inset-x-0 bottom-0 sm:hidden p-3 bg-white border-t">
  <div class="max-w-xl mx-auto flex gap-3">
    <button onclick="document.getElementById('movForm').submit()" class="flex-1 py-3 bg-blue-600 text-white">Salvar</button>
  </div>
</div>

Modal de edição (abrir via botão com data-attributes):

Botão abre modal:

<button class="btn-open-edit" data-update-route="{{ route('movimentacao.update', $m->id) }}" data-km_inicial="{{ $m->km_inicial }}" ...>Alterar</button>

Modal no fim da página contém <form id="editForm" method="POST"> e script que preenche campos e define form.action = updateRoute.

Notas de segurança:

Sempre inclua @csrf.

Para PUT/PATCH/DELETE use @method('PUT') dentro do form.

Charts (Chart.js) — integração

No dashboard usamos Chart.js via CDN:

Inserir <canvas id="chartId"></canvas> onde o gráfico deve aparecer.

Passar dados do controller para a view com compact(...) e usar @json($array) para injetar JSON safely no JS.

Exemplo de uso (no @push('scripts')):

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels = @json($dates);
const data = @json($kmPerDay);
const ctx = document.getElementById('chartKmPerDay').getContext('2d');
new Chart(ctx, { type:'line', data: { labels, datasets:[{ label:'KM', data }] } });
</script>

Se migrar para build com npm, instale chart.js e importe via ES modules.

Sidebar / Layout / Modais — como funcionam

Sidebar: categorias colapsáveis com persistência em localStorage. A lógica está em layouts.app (JS).

Busca na sidebar: input #sidebarSearch filtra links via texto.

Modais: abrimos o modal alterando classes hidden/flex e setando form.action dinamicamente. Os botões que abrem o modal têm attributes data-* com os valores a popular.

Controllers & Routes importantes

DashboardController@index — dashboard (rota '/' ou /dashboard).

MovimentacaoController@store, @update, @index, @destroy — CRUD de movimentações.

VeiculoController@... — CRUD de veículos.

UserController — gerencia motoristas (usuários do sistema).

Rotas usadas nas views: route('movimentacao.index'), route('movimentacao.store'), route('movimentacao.update', $id), route('veiculo.index'), route('tipoVeiculo.index'), route('user.index').

Adicione no routes/web.php:

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('movimentacao', MovimentacaoController::class);
Route::resource('veiculo', VeiculoController::class);
Route::resource('user', UserController::class);
Route::resource('tipoVeiculo', TipoVeiculoController::class);

Ajuste middleware(['auth']) conforme necessidade.

Models & Relacionamentos essenciais

Movimentacao.php

class Movimentacao extends Model
{
    protected $table = 'movimentacoes';
    protected $fillable = ['data','hora','veiculo_id','user_id','km_inicial','km_final','km_rodado','origem','destino','status','observacao'];

    public function veiculo() { return $this->belongsTo(\App\Models\Veiculo::class); }
    public function user() { return $this->belongsTo(\App\Models\User::class); }
}

Veiculo.php

public function tipo() { return $this->belongsTo(\App\Models\TipoVeiculo::class, 'tipo_veiculo_id');}
public function movimentacoes() { return $this->hasMany(Movimentacao::class); }

User.php

Use relacionamento hasMany se precisar: $this->hasMany(Movimentacao::class).

Deploy rápido / Produção

Configure .env para produção (DB, CACHE_DRIVER, SESSION_DRIVER).

Compile assets (npm run build) e faça php artisan config:cache && php artisan route:cache.

Garanta regras de permissão em storage e bootstrap/cache:

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

Use um servidor (Nginx + php-fpm) ou Forge / Ploi / VPS.

Se usar HTTPS, configure certificados.

Solução de problemas comuns

Erro de FK ao migrar: verifique ordem das migrations e foreign key checks. Pode ser que o banco já tenha tabelas (drop e recreate).

502 / 500: ver logs storage/logs/laravel.log.

Permissão em storage: chmod -R 775 storage bootstrap/cache.

Composer memory: COMPOSER_MEMORY_LIMIT=-1 composer install.

Assets não aparecem: confere @stack('scripts') no layout; se usar CDN, verifique se script está carregado depois do HTML.

Comandos úteis
# instalação
composer install
npm install

# DB
php artisan migrate
php artisan db:seed

# dev
php artisan serve
npm run dev

# produção
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache