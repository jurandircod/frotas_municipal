<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>@yield('title', config('app.name', 'Sistema de Frota'))</title>

    {{-- Tailwind CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

    <style>
        /* safe area */
        .safe-area { padding-bottom: env(safe-area-inset-bottom); }

        /* header height */
        :root { --header-h: 56px; } /* h-14 */
        header { height: var(--header-h); }

        /* main offset to avoid header overlap + sidebar offset on md+ */
        main { padding-top: calc(var(--header-h) + 0.75rem); } /* pequeno espaçamento extra */
        @media (min-width: 768px) {
            main { padding-top: calc(var(--header-h) + 1rem); margin-left: 16rem; } /* sidebar w-64 = 16rem */
        }

        /* overlay transition helper */
        .translate-x-0 { transform: translateX(0); }
        .-translate-x-full { transform: translateX(-100%); }
    </style>

    @stack('styles')
</head>
<body class="antialiased text-gray-800 bg-gray-50 min-h-screen">

    {{-- HEADER --}}
    <header class="fixed inset-x-0 top-0 z-40 bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
            <div class="h-full flex items-center justify-between">
                <div class="flex items-center gap-1">
                    <button id="btnOpenSidebar" class="inline-flex items-center justify-center p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <span class="sr-only">Abrir menu</span>
                    </button>
                    {{-- Mobile: botão abrir sidebar --}}
                </div>
                
                {{-- Centro (breadcrumb opcional) --}}
                <div class="hidden md:flex md:flex-1 md:justify-center">
                    <div class="text-sm font-medium text-gray-700">@yield('page_header', '')</div>
                </div>
                
                {{-- Ações do usuário --}}
                <div class="flex items-center gap-3">
                    <button class="hidden md:inline-flex items-center justify-center p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </button>

                    <div class="relative">
                        <button id="userMenuBtn" class="flex items-center gap-2 p-1 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <span class="hidden md:inline-block text-sm text-gray-700">Olá, <strong class="font-medium">{{ Auth::user()->name ?? 'Usuário' }}</strong></span>
                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v2h20v-2c0-3.3-6.7-5-10-5z" />
                                </svg>
                            </div>
                        </button>

                        <div id="userMenu" class="hidden absolute right-0 mt-2 w-40 bg-white border border-gray-100 rounded-md shadow-lg py-1 overflow-hidden">
                            <a href="{{ route('user.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Perfil</a>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Sair</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- SIDEBAR (desktop fixed) --}}
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-100 shadow-md transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out">
        <div class="h-full overflow-y-auto">
            <div class="px-4 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-prefeitura.png') }}" alt="Logo" onerror="this.style.display='none'" class="h-10 w-10 rounded-md object-contain border bg-white p-1">
                    <div>
                        <div class="text-sm font-semibold">{{ config('app.name', 'Prefeitura') }}</div>
                        <div class="text-xs text-gray-500">Gestão de Frota</div>
                    </div>
                </div>
            </div>

            <nav class="px-2 py-4 space-y-1">
                <a href="{{ route('movimentacao.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->routeIs('movimentacao.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">
                    Movimentações
                </a>
                <a href="{{ route('user.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->routeIs('user.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">
                    Motoristas
                </a>
                <a href="{{ route('veiculo.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->routeIs('veiculo.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">
                    Veículos
                </a>
                <a href="{{ route('tipoVeiculo.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->routeIs('tipoVeiculo.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">
                    Tipos
                </a>

                {{-- separador --}}
                <div class="border-t border-gray-100 my-3"></div>

                {{-- ações rápidas --}}
                <a href="" class="block px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50">Relatórios</a>
                <a href="" class="block px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50">Configurações</a>
            </nav>

            {{-- rodapé da sidebar --}}
            <div class="mt-auto p-4 border-t border-gray-100">
                <div class="text-xs text-gray-500">Versão: <strong class="text-gray-700">1.0.0</strong></div>
            </div>
        </div>
    </aside>

    {{-- overlay mobile (quando sidebar aberto) --}}
    <div id="sidebarOverlay" class="fixed inset-0 z-40 bg-black bg-opacity-40 hidden md:hidden"></div>

    {{-- Logout form --}}
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>

    {{-- MAIN --}}
    <main class="safe-area">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page header --}}
            <div class="pt-4 sm:pt-6 pb-4">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">@yield('title', '')</h2>
                        <p class="text-sm text-gray-500">@yield('subtitle', '')</p>
                    </div>
                    <div class="hidden md:block">
                        @yield('page_actions')
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <section class="bg-white rounded-2xl shadow-sm p-4 sm:p-6">
                @yield('content')
            </section>

            {{-- Mobile quick actions --}}
            <div class="md:hidden mt-6">
                <div class="flex gap-3">
                    @yield('mobile_quick_actions')
                </div>
            </div>
        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="mt-8 py-6 text-center text-xs text-gray-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div>Prefeitura Municipal — Sistema de Gestão de Frota</div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script>
        (function () {
            const sidebar = document.getElementById('sidebar');
            const btnOpen = document.getElementById('btnOpenSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const userBtn = document.getElementById('userMenuBtn');
            const userMenu = document.getElementById('userMenu');

            function openSidebar() {
                if (sidebar) sidebar.classList.remove('-translate-x-full');
                if (overlay) overlay.classList.remove('hidden');
            }
            function closeSidebar() {
                if (sidebar) sidebar.classList.add('-translate-x-full');
                if (overlay) overlay.classList.add('hidden');
            }

            if (btnOpen) {
                btnOpen.addEventListener('click', function () {
                    // abrir/fechar
                    if (sidebar.classList.contains('-translate-x-full')) openSidebar();
                    else closeSidebar();
                });
            }

            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }

            // Fecha sidebar mobile automaticamente ao redimensionar para desktop (md = 768px)
            window.addEventListener('resize', function () {
                if (window.innerWidth >= 768) {
                    // garante que a sidebar esteja visível no desktop (remove classe que esconde)
                    if (sidebar) sidebar.classList.remove('-translate-x-full');
                    if (overlay) overlay.classList.add('hidden');
                } else {
                    // em mobile, manter sidebar fechada por padrão
                    if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
                        // opcional: deixar do jeito que estiver; aqui não forçamos fechamento
                    }
                }
            });

            // Fecha sidebar quando um link for clicado (apenas em mobile)
            document.querySelectorAll('#sidebar a').forEach(function (el) {
                el.addEventListener('click', function () {
                    if (window.innerWidth < 768) closeSidebar();
                });
            });

            // User dropdown toggle
            if (userBtn && userMenu) {
                userBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    userMenu.classList.toggle('hidden');
                });
                document.addEventListener('click', function (e) {
                    if (!userBtn.contains(e.target) && !userMenu.contains(e.target)) {
                        userMenu.classList.add('hidden');
                    }
                });
            }

            // Notyf alerts
            const notyf = new Notyf({
                duration: 4000,
                position: { x: 'right', y: 'bottom' },
                dismissible: true,
                types: [
                    { type: 'info', background: '#3b82f6', icon: false },
                    { type: 'warning', background: '#f59e0b', icon: false }
                ]
            });

            @if (session('success'))
                notyf.success('{{ session('success') }}');
            @endif
            @if (session('error'))
                notyf.error('{{ session('error') }}');
            @endif
            @if (session('info'))
                notyf.open({ type: 'info', message: '{{ session('info') }}' });
            @endif
            @if (session('warning'))
                notyf.open({ type: 'warning', message: '{{ session('warning') }}' });
            @endif
        })();
    </script>

    @stack('scripts')
</body>
</html>