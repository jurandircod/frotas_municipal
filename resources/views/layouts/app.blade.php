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
    <link rel="shortcut icon" href="{{ asset('logoprefeitura.png') }}">

    <style>
        /* safe area */
        .safe-area {
            padding-bottom: env(safe-area-inset-bottom);
        }

        :root {
            --header-h: 56px;
        }

        header {
            height: var(--header-h);
        }

        main {
            padding-top: calc(var(--header-h) + 0.75rem);
        }

        @media (min-width: 768px) {
            main {
                padding-top: calc(var(--header-h) + 1rem);
                margin-left: 16rem;
            }
        }

        .translate-x-0 {
            transform: translateX(0);
        }

        .-translate-x-full {
            transform: translateX(-100%);
        }

        .chev {
            transition: transform .15s ease;
        }

        .chev-open {
            transform: rotate(90deg);
        }
    </style>

    @stack('styles')
</head>

<body class="antialiased text-gray-800 bg-gray-50 min-h-screen">

    {{-- HEADER --}}
    <header class="fixed inset-x-0 top-0 z-40 bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
            <div class="h-full flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <button id="btnOpenSidebar" aria-expanded="false"
                        class="inline-flex items-center justify-center p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <span class="sr-only">Abrir menu</span>
                    </button>

                    <a href="{{ url('/') }}" class="flex items-center gap-2 ml-1">
                        <img src="{{ asset('images/logo-prefeitura.png') }}" alt="Logo"
                            onerror="this.style.display='none'"
                            class="h-8 w-8 rounded-md object-contain border bg-white p-1">
                        <span
                            class="hidden sm:inline-block text-sm font-semibold">{{ config('app.name', 'Prefeitura') }}</span>
                    </a>
                </div>

                <div class="hidden md:flex md:flex-1 md:justify-center">
                    <div class="text-sm font-medium text-gray-700">@yield('page_header', '')</div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="relative">
                        <button id="userMenuBtn" aria-haspopup="true" aria-expanded="false"
                            class="flex items-center gap-2 p-1 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <span class="hidden md:inline-block text-sm text-gray-700">Olá, <strong
                                    class="font-medium">{{ Auth::user()->name ?? 'Usuário' }}</strong></span>
                            <div
                                class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path
                                        d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v2h20v-2c0-3.3-6.7-5-10-5z" />
                                </svg>
                            </div>
                        </button>

                        <div id="userMenu"
                            class="hidden absolute right-0 mt-2 w-40 bg-white border border-gray-100 rounded-md shadow-lg py-1 overflow-hidden">
                            <a href="{{ route('user.index') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Perfil</a>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Sair</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- SIDEBAR --}}
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-100 shadow-md transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out">
        <div class="h-full flex flex-col">
            <div class="px-4 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-prefeitura.png') }}" alt="Logo Prefeitura"
                        onerror="this.style.display='none'"
                        class="h-10 w-10 rounded-md object-contain border bg-white p-1">
                    <div class="flex-1">
                        <div class="text-sm font-semibold">{{ config('app.name', 'Prefeitura') }}</div>
                        <div class="text-xs text-gray-500">Gestão de Frota</div>
                    </div>
                </div>

                <div class="mt-3">
                    <label for="sidebarSearch" class="sr-only">Pesquisar</label>
                    <div class="relative">
                        <input id="sidebarSearch" type="search" placeholder="Pesquisar menu"
                            class="w-full rounded-md border-gray-200 bg-gray-50 text-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <button id="clearSidebarSearch" type="button"
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 text-sm hidden">✕</button>
                    </div>
                </div>
            </div>

            {{-- Navigation grouped --}}
            <nav class="px-2 py-4 overflow-y-auto space-y-2 flex-1" aria-label="Sidebar">

                @php
                    // pega o usuário autenticado (garanta que auth está disponível)
                    $user = auth()->user();
                @endphp

                {{-- Fleet group --}}
                <div class="px-1">
                    <button type="button"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-md hover:bg-gray-50 focus:outline-none"
                        data-toggle-group="fleet" aria-expanded="true">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7h18M5 7v13a1 1 0 001 1h2a1 1 0 001-1V7M15 7v13a1 1 0 001 1h2a1 1 0 001-1V7">
                                </path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Frota</span>
                        </div>
                        <svg class="h-4 w-4 chev text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M6.293 9.293a1 1 0 011.414 0L10 11.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div class="mt-1 space-y-1 pl-8" data-group="fleet">
                        {{-- Veículos e Tipos SÓ para admin --}}
                        @if ($user && $user->role_id == 2)
                            <a href="{{ route('veiculo.index') }}"
                                class="block px-3 py-2 rounded-md text-sm {{ request()->routeIs('veiculo.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">Veículos</a>
                            <a href="{{ route('veiculo.list') }}"
                                class="block px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50">Listar
                                veículos</a>
                            <a href="{{ route('tipoVeiculo.index') }}"
                                class="block px-3 py-2 rounded-md text-sm {{ request()->routeIs('tipoVeiculo.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">Tipos
                                de veículo</a>
                            <a href="{{ route('tipoVeiculo.index') }}"
                                class="block px-3 py-2 rounded-md text-sm {{ request()->routeIs('tipoVeiculo.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">Listar
                                Tipos de Veículo</a>
                        @else
                            {{-- Usuário comum não vê itens de frota --}}
                            <div class="text-xs text-gray-400 italic px-3 py-2">Acesso a frota restrito ao
                                administrador</div>
                        @endif
                    </div>
                </div>

                {{-- Operations group --}}
                <div class="px-1">
                    <button type="button"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-md hover:bg-gray-50 focus:outline-none"
                        data-toggle-group="operations" aria-expanded="true">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Operações</span>
                        </div>
                        <svg class="h-4 w-4 chev text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M6.293 9.293a1 1 0 011.414 0L10 11.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div class="mt-1 space-y-1 pl-8" data-group="operations">
                        {{-- Movimentações: ADMIN vê listagens; COMUM vê apenas o link para abrir/criar movimentação --}}
                        @if ($user && $user->role_id == 2)
                            <a href="{{ route('movimentacao.index') }}"
                                class="block px-3 py-2 rounded-md text-sm {{ request()->routeIs('movimentacao.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">Movimentações</a>
                            <a href="{{ route('movimentacao.list') }}"
                                class="block px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50">Listar
                                Movimentações</a>
                        @else
                            {{-- Usuário comum: link direto para página onde ele cria movimentação.
                                 Ajuste a URL abaixo para onde seu formulário de criação realmente está. --}}
                            <a href="{{ url('/movimentacao') }}"
                                class="block px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50">Nova
                                Movimentação</a>
                        @endif

                        {{-- Motorista / perfil: ambos podem ver --}}
                        <a href="{{ route('user.index') }}"
                            class="block px-3 py-2 rounded-md text-sm {{ request()->routeIs('user.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600' }}">Motorista</a>
                    </div>
                </div>

                {{-- Admin group: completamente escondido para não-admin --}}
                @if ($user && $user->role_id == 2)
                    <div class="px-1">
                        <button type="button"
                            class="w-full flex items-center justify-between px-3 py-2 rounded-md hover:bg-gray-50 focus:outline-none"
                            data-toggle-group="admin" aria-expanded="false">
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3zM6.5 20h11a2 2 0 002-2v-2a4 4 0 00-4-4h-7a4 4 0 00-4 4v2a2 2 0 002 2z" />
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Admin</span>
                            </div>
                            <svg class="h-4 w-4 chev text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M6.293 9.293a1 1 0 011.414 0L10 11.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div class="mt-1 space-y-1 pl-8" data-group="admin">
                            <a href="{{ route('user.list') }}"
                                class="block px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50">Usuários</a>
                            <a href="{{ route('secretaria.index') }}"
                                class="block px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50">Secretarias</a>
                            <a href="#"
                                class="block px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50">Relatórios</a>
                            <a href="#"
                                class="block px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50">Configurações</a>
                        </div>
                    </div>
                @endif

                <div class="border-t border-gray-100 my-3"></div>

                <div class="px-1">
                    <a href="#"
                        class="block px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50">Ajuda</a>
                    <a href="#"
                        class="block px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50">Documentação</a>
                </div>

            </nav>

            <div class="p-4 border-t border-gray-100">
                <div class="text-xs text-gray-500">Versão: <strong class="text-gray-700">1.0.0</strong></div>
            </div>
        </div>
    </aside>

    <div id="sidebarOverlay" class="fixed inset-0 z-40 bg-black bg-opacity-40 hidden md:hidden" aria-hidden="true">
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>

    <main class="safe-area">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="pt-4 sm:pt-6 pb-4">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">@yield('title', '')</h2>
                        <p class="text-sm text-gray-500">@yield('subtitle', '')</p>
                    </div>
                    <div class="hidden md:block">@yield('page_actions')</div>
                </div>
            </div>

            <section class="bg-white rounded-2xl shadow-sm p-4 sm:p-6">
                @yield('content')
            </section>

            <div class="md:hidden mt-6">
                <div class="flex gap-3">@yield('mobile_quick_actions')</div>
            </div>
        </div>
    </main>

    <footer class="mt-8 py-6 text-center text-xs text-gray-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div>Prefeitura Municipal — Sistema de Gestão de Frota</div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script>
        (function() {
            const sidebar = document.getElementById('sidebar');
            const btnOpen = document.getElementById('btnOpenSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const userBtn = document.getElementById('userMenuBtn');
            const userMenu = document.getElementById('userMenu');
            const sidebarSearch = document.getElementById('sidebarSearch');
            const clearSearchBtn = document.getElementById('clearSidebarSearch');

            function openSidebar() {
                if (sidebar) sidebar.classList.remove('-translate-x-full');
                if (overlay) overlay.classList.remove('hidden');
                if (btnOpen) btnOpen.setAttribute('aria-expanded', 'true');
            }

            function closeSidebar() {
                if (sidebar) sidebar.classList.add('-translate-x-full');
                if (overlay) overlay.classList.add('hidden');
                if (btnOpen) btnOpen.setAttribute('aria-expanded', 'false');
            }

            if (btnOpen) {
                btnOpen.addEventListener('click', function() {
                    if (sidebar.classList.contains('-translate-x-full')) openSidebar();
                    else closeSidebar();
                });
            }

            if (overlay) overlay.addEventListener('click', closeSidebar);

            document.querySelectorAll('#sidebar a').forEach(function(el) {
                el.addEventListener('click', function() {
                    if (window.innerWidth < 768) closeSidebar();
                });
            });

            if (userBtn && userMenu) {
                userBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenu.classList.toggle('hidden');
                    const expanded = userMenu.classList.contains('hidden') ? 'false' : 'true';
                    userBtn.setAttribute('aria-expanded', expanded);
                });
                document.addEventListener('click', function(e) {
                    if (!userBtn.contains(e.target) && !userMenu.contains(e.target)) userMenu.classList.add(
                        'hidden');
                });
            }

            const groupToggles = document.querySelectorAll('[data-toggle-group]');
            groupToggles.forEach(btn => {
                const key = 'sidebar_group_' + (btn.getAttribute('data-toggle-group') || btn.textContent
            .trim());
                const targetName = btn.getAttribute('data-toggle-group');
                const panel = document.querySelector('[data-group="' + targetName + '"]');
                const stored = localStorage.getItem(key);
                if (stored === 'open') {
                    panel && panel.classList.remove('hidden');
                    btn.querySelector('.chev') && btn.querySelector('.chev').classList.add('chev-open');
                } else if (stored === 'closed') {
                    panel && panel.classList.add('hidden');
                    btn.querySelector('.chev') && btn.querySelector('.chev').classList.remove('chev-open');
                } else {
                    if (targetName === 'admin') {
                        panel && panel.classList.add('hidden');
                        btn.querySelector('.chev') && btn.querySelector('.chev').classList.remove('chev-open');
                    } else {
                        panel && panel.classList.remove('hidden');
                        btn.querySelector('.chev') && btn.querySelector('.chev').classList.add('chev-open');
                    }
                }

                btn.addEventListener('click', function() {
                    if (!panel) return;
                    const isHidden = panel.classList.contains('hidden');
                    if (isHidden) {
                        panel.classList.remove('hidden');
                        localStorage.setItem(key, 'open');
                        btn.querySelector('.chev') && btn.querySelector('.chev').classList.add(
                            'chev-open');
                    } else {
                        panel.classList.add('hidden');
                        localStorage.setItem(key, 'closed');
                        btn.querySelector('.chev') && btn.querySelector('.chev').classList.remove(
                            'chev-open');
                    }
                });
            });

            if (sidebarSearch) {
                const anchors = Array.from(document.querySelectorAll('#sidebar a'));
                const normalize = (s) => s ? s.toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu, '') : '';
                sidebarSearch.addEventListener('input', function() {
                    const q = normalize(this.value.trim());
                    clearSearchBtn.classList.toggle('hidden', q.length === 0);
                    anchors.forEach(a => {
                        const text = normalize(a.textContent || '');
                        a.style.display = (q === '' || text.includes(q)) ? '' : 'none';
                    });
                });
                clearSearchBtn && clearSearchBtn.addEventListener('click', function() {
                    sidebarSearch.value = '';
                    sidebarSearch.dispatchEvent(new Event('input'));
                    sidebarSearch.focus();
                });
            }

            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    sidebar && sidebar.classList.remove('-translate-x-full');
                    overlay && overlay.classList.add('hidden');
                }
            });

            const notyf = new Notyf({
                duration: 4000,
                position: {
                    x: 'right',
                    y: 'top'
                },
                dismissible: true
            });
            @if (session('success'))
                notyf.success('{{ session('success') }}');
            @endif
            @if (session('error'))
                notyf.error('{{ session('error') }}');
            @endif
            @if (session('info'))
                notyf.open({
                    type: 'info',
                    message: '{{ session('info') }}'
                });
            @endif
            @if (session('warning'))
                notyf.open({
                    type: 'warning',
                    message: '{{ session('warning') }}'
                });
            @endif
        })();
    </script>

    @stack('scripts')
</body>

</html>
