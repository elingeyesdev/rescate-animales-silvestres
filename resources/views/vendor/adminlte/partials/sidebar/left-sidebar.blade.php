<aside class="main-sidebar {{ config('adminlte.classes_sidebar') }}">

    {{-- Sidebar brand logo --}}
    @if(config('adminlte.logo_img_xl'))
        @include('adminlte::partials.common.brand-logo-xl')
    @else
        @include('adminlte::partials.common.brand-logo-xs')
    @endif

    {{-- Sidebar menu --}}
    <div class="sidebar">

        {{-- User Panel --}}
        @if(Auth::check())
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ Auth::user()->adminlte_image() }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="{{ url(Auth::user()->adminlte_profile_url()) }}" class="d-block">{{ Auth::user()->name }}</a>
                <span class="d-block text-muted small">{{ Auth::user()->adminlte_desc() }}</span>
            </div>
        </div>
        @endif

        {{-- Search Form --}}
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Buscar" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column {{ config('adminlte.classes_sidebar_nav') }}"
                data-widget="treeview" role="menu"
                @if(config('adminlte.sidebar_nav_accordion')) data-accordion="false" @endif
                @if(config('adminlte.sidebar_nav_animation_speed') != 300) data-animation-speed="{{ config('adminlte.sidebar_nav_animation_speed') }}" @endif>

                {{-- Configured sidebar links --}}
                @each('adminlte::partials.sidebar.menu-item', $adminlte->menu('sidebar'), 'item')

            </ul>
        </nav>
    </div>

</aside>