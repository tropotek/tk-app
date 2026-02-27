<header>
    <nav class="navbar navbar-expand-sm fixed-top mb-2 navbar-light bg-light shadow-sm">
        <div class="{{ config('app.resources.layout', 'container') }}">
            <a class="navbar-brand" href="/">{{ config('app.name', 'Example') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse"
                    aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav me-auto mb-2 mb-sm-0">

                    @foreach (menu('NavBar')->getChildren() as $item)
                        {{-- Pass initial classes to the component --}}
                        <x-tk-base::navitem :item="$item" level="0" class="nav-item" submenu-class="dropdown-menu" link-class="nav-link" />
                    @endforeach

                    @auth
                        <li class="nav-item dropdown d-block d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                <span>{{ Auth::user()->name }}</span>
                            </a>
                            <x-usernav />
                        </li>
                    @endauth
                </ul>

                @auth
                    <form class="d-flex me-md-3" role="search" action="/search" method="POST">
                        <input type="hidden" name="_csrf_token" value="21fffd6b83d2c79e4a77a853a2b8e016">
                        <div class="input-group input-group-sm">
                            <input class="form-control" type="search" name="s" placeholder="Search" aria-label="Search">
                            <button class="btn btn-outline-secondary border-light-subtle" type="submit" name="action"
                                    value="search" title="Find a page" id="btn-search"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                @endauth

                @auth
                    <div class="dropdown text-end d-none d-sm-block">
                        <a href="#" class="link-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown"
                           aria-expanded="false">
                            <img src="{{asset('/img/mdo.jpeg') }}" alt="mdo" width="32" height="32" class="rounded-circle" />
                            <span>{{ Auth::user()->name }}</span>
                        </a>
                        <x-usernav class="dropdown-menu-end" />
                    </div>
                @else
                    <div class="dropdown">
                        <ul class="navbar-nav me-auto mb-2 mb-md-0">
                            <li class="nav-item"><a class="nav-link" href="/login">Login</a></li>
                        </ul>
                    </div>
                @endauth

            </div>
        </div>
    </nav>
</header>
