@php
    $modules = dynamic_sidebar();
    $user = auth()->user();

    $isAdmin = $user->hasRole('admin');

    $company = $user->company;

    $isAdmin = $user->hasRole(['admin']);


    $lockedModules = ['services.index', 'services.create', 'contractors.index', 'contractors.create', 'products.index', 'products.create'];
@endphp

<div class="sidebar-wrapper" data-sidebar-layout="stroke-svg">
    <div class="logo-wrapper">
        <a href="{{ route('admin.dashboard') }}">
            <img class="img-fluid for-light reap433-admin-logo" src="{{ asset('assets/web/images/logo.png') }}" alt="REAP433" />
            <img class="img-fluid for-dark reap433-admin-logo" src="{{ asset('assets/web/images/logo.png') }}" alt="REAP433" />
        </a>
        <div class="back-btn"><i class="fa-solid fa-angle-left"></i></div>
        {{-- <div class="toggle-sidebar">
            <i class="status_toggle middle sidebar-toggle" data-feather="grid"></i>
        </div> --}}
    </div>
    {{-- <div class="logo-icon-wrapper">
        <a href="">
            <img class="img-fluid" src="{{ asset('/images/logo.png') }}" alt="" />
        </a>
    </div> --}}
    <nav class="sidebar-main">
        <div class="left-arrow" id="left-arrow">
            <i data-feather="arrow-left"></i>
        </div>
        <div id="sidebar-menu">
            <ul class="sidebar-links" id="simple-bar">
                <li class="back-btn">
                    {{-- <a href="">
                        <img class="img-fluid" src="{{ asset('images/logo.png') }}" alt="" />
                    </a> --}}
                    <div class="mobile-back text-end">
                        <span>Back</span><i class="fa-solid fa-angle-right ps-2" aria-hidden="true"></i>
                    </div>
                </li>
                @foreach ($modules->reject(fn ($module) => $module->route_name === 'products-module') as $module)
                    @php
                        $hasChildren = $module->children && $module->children->count() > 0;
                        $isChildRouteActive = static function (string $routeName): bool {
                            if (! Route::has($routeName)) {
                                return false;
                            }

                            return request()->routeIs($routeName)
                                || request()->routeIs(preg_replace('/\.index$/', '.*', $routeName) ?: $routeName);
                        };
                        $childActive = $hasChildren
                            && $module->children->contains(fn ($child) => $isChildRouteActive($child->route_name));
                        $moduleActive = ! $hasChildren && $isChildRouteActive($module->route_name);
                        // $isLocked = $isProfileLocked && in_array($module->route_name, $lockedModules);
                    @endphp

                    <li class="sidebar-list">
                        <a
                            href="{{ $hasChildren ? '#' : (Route::has($module->route_name) ? route($module->route_name) : '#') }}"
                            class="sidebar-link sidebar-title {{ $hasChildren ? ($childActive ? 'active' : '') : ($moduleActive ? 'active link-nav' : 'link-nav') }}"
                            @if($hasChildren) aria-expanded="{{ $childActive ? 'true' : 'false' }}" @endif
                        >
                            <span class="theme-icons">
                                <i class="{{ $module->icon }}"></i>
                            </span>

                            <span>{{ $module->name }}</span>

                            @if ($hasChildren)
                                <div class="according-menu">
                                    <i class="fa-solid fa-angle-{{ $childActive ? 'down' : 'right' }}"></i>
                                </div>
                            @endif
                        </a>

                        @if ($hasChildren)
                            <ul class="sidebar-submenu" @if($childActive) style="display: block;" @endif>
                                @foreach ($module->children as $child)
                                    @php
                                        $submenuActive = $isChildRouteActive($child->route_name);
                                    @endphp
                                    {{-- @php
                                        $childLocked = $isProfileLocked && in_array($child->route_name, $lockedModules);
                                    @endphp --}}

                                    <li @class(['active' => $submenuActive])>
                                        <a
                                            href="{{ Route::has($child->route_name) ? route($child->route_name) : '#' }}"
                                            @class(['active' => $submenuActive])
                                        >
                                            {{ $child->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach

            </ul>
        </div>
        <div class="right-arrow" id="right-arrow">
            <i data-feather="arrow-right"></i>
        </div>
    </nav>
</div>
