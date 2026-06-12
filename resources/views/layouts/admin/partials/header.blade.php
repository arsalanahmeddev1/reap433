<div class="page-header">
    <div class="header-wrapper row m-0">
        <div class="col-4">
            {{-- @php
                $userRole = Auth::user()->roles->first();
                $roleName = $userRole ? $userRole->name : 'user';
            @endphp --}}
            {{-- <h3 class="">{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $roleName)) }}</h3> --}}
        </div>
        <div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
            <ul class="nav-menus">
                <!-- <li class="onhover-dropdown">
                    <div class="notification-box">
                        <svg>
                            <use href="{{ asset('assets/libs/svg/icon-sprite.svg#notification') }}"></use>
                        </svg><span class="badge rounded-pill badge-success">4 </span>
                    </div>
                    <div class="onhover-show-div notification-dropdown">
                        <h6 class="f-18 mb-0 dropdown-title">Notifications</h6>
                        <ul>
                            <li class="b-l-primary border-4 toast default-show-toast align-items-center text-light border-0 fade show"
                                aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
                                <div class="d-flex justify-content-between">
                                    <div class="toast-body">
                                        <p>Delivery processing</p>
                                    </div>
                                    <button class="btn-close btn-close-white me-2 m-auto" type="button"
                                        data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                            </li>
                            <li class="b-l-success border-4 toast default-show-toast align-items-center text-light border-0 fade show"
                                aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
                                <div class="d-flex justify-content-between">
                                    <div class="toast-body">
                                        <p>Order Complete</p>
                                    </div>
                                    <button class="btn-close btn-close-white me-2 m-auto" type="button"
                                        data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                            </li>
                            <li class="b-l-secondary border-4 toast default-show-toast align-items-center text-light border-0 fade show"
                                aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
                                <div class="d-flex justify-content-between">
                                    <div class="toast-body">
                                        <p>Tickets Generated</p>
                                    </div>
                                    <button class="btn-close btn-close-white me-2 m-auto" type="button"
                                        data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                            </li>
                            <li class="b-l-warning border-4 toast default-show-toast align-items-center text-light border-0 fade show"
                                aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
                                <div class="d-flex justify-content-between">
                                    <div class="toast-body">
                                        <p>Delivery Complete</p>
                                    </div>
                                    <button class="btn-close btn-close-white me-2 m-auto" type="button"
                                        data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li> -->
                <li class="profile-nav onhover-dropdown pe-0 py-0">
                    <div class="d-flex profile-media">
                        <img class="b-r-10" src="{{ asset('assets/libs/images/dashboard/profile.png') }}" alt="" />
                        <div class="flex-grow-1 d-flex align-items-center justify-content-between gap-1">
                            <span>
                                {{ Auth::user()?->name }}
                            </span>
                            <i class="middle fa-solid fa-angle-down"></i>
                        </div>
                    </div>
                    <ul class="profile-dropdown onhover-show-div">
                        <li>
                            <a href="{{ route('home') }}" target="_blank"><i data-feather="globe"></i><span>Website </span></a>
                        </li>
                        {{-- <li>
                            <a href="{{ route('profile') }}"><i data-feather="user"></i><span>Update Profile
                        </span></a>
                </li> --}}
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item"><i data-feather="log-in"> </i><span>Log
                                out</span></button>
                    </form>
                </li>
            </ul>
            </li>
            <li class="theme-toggle-wrap d-flex align-items-center ps-2 ms-1">
                <button
                    type="button"
                    id="themeToggle"
                    class="theme-toggle-btn toggleThemeBtn"
                    aria-label="Toggle color theme"
                    title="Theme">
                    <span class="theme-toggle-track" aria-hidden="true">
                        <span class="theme-toggle-thumb">
                            <i class="fa-solid fa-sun"></i>
                            <i class="fa-solid fa-moon"></i>
                        </span>
                    </span>
                </button>
            </li>
            </ul>
        </div>
        <script class="result-template" type="text/x-handlebars-template">
            <div class="ProfileCard u-cf">
              <div class="ProfileCard-avatar"><svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  class="feather feather-airplay m-0"
                ><path
                    d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"
                  ></path><polygon
                    points="12 15 17 21 7 21 12 15"
                  ></polygon></svg></div>
              <div class="ProfileCard-details">
                <div class="ProfileCard-realName"></div>
              </div>
            </div>
          </script>
        <script class="empty-template" type="text/x-handlebars-template">
            <div class="EmptyMessage">Your search turned up 0 results. This most
              likely means the backend is down, yikes!</div>
          </script>
    </div>
</div>