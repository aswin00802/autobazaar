<nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
        <i class="icon-base ri ri-menu-line icon-22px"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
        <!-- Search -->
        <div class="navbar-nav align-items-center flex-grow-1 me-3">
            <div class="admin-search w-100" id="adminSearch" data-url="<?php echo e(route('admin.search')); ?>">
                <i class="icon-base ri ri-search-line icon-20px admin-search-icon"></i>
                <input type="search" class="form-control admin-search-input" id="adminSearchInput"
                       placeholder="Search users, autos, rides, orders…  ( / )" autocomplete="off"
                       aria-label="Search" aria-controls="adminSearchResults" aria-expanded="false" />
                <div class="admin-search-results shadow" id="adminSearchResults" role="listbox" hidden></div>
            </div>
        </div>

        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center ms-md-auto">

            <!-- Light / dark -->
            <li class="nav-item me-2">
                <button type="button" class="btn btn-icon btn-text-secondary rounded-pill" id="themeToggle" title="Switch between light and dark" aria-label="Switch between light and dark">
                    <i class="icon-base ri ri-moon-clear-line icon-22px" id="themeToggleIcon"></i>
                </button>
            </li>

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="<?php echo e(asset('admin/assets/img/avatars/1.png')); ?>" alt="avatar" class="rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end mt-3 py-2">
                <li>
                    <a class="dropdown-item" href="<?php echo e(route('dashboard')); ?>">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-2">
                                <div class="avatar avatar-online">
                                    <img src="<?php echo e(asset('admin/assets/img/avatars/1.png')); ?>" alt="alt" class="w-px-40 h-auto rounded-circle" />
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 small"><?php echo e(Auth::user()->name); ?></h6>
                                <small class="text-body-secondary"><?php echo e(Auth::user()->role->name); ?></small>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <div class="dropdown-divider"></div>
                </li>
                <!-- <li>
                    <a class="dropdown-item" href="javascript:void(0)">
                        <i class="icon-base ri ri-user-3-line icon-22px me-3"></i><span class="align-middle">My Profile</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="javascript:void(0)">
                        <i class="icon-base ri ri-settings-4-line icon-22px me-3"></i><span class="align-middle">Settings</span>
                    </a>
                </li> -->
                
                <li>
                    <a class="dropdown-item" href="javascript:void(0)"> <i class="icon-base ri ri-rotate-lock-fill icon-22px me-3"></i><span>Change Password</span> </a>
                </li>

                <li>
                    <div class="d-grid px-4 pt-2 pb-1">
                        <a class="btn btn-sm btn-danger d-flex" href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" >
                            <small class="align-middle">Logout</small>
                            <i class="icon-base ri ri-logout-box-r-line ms-2 icon-16px"></i>
                        </a>
                        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                            <?php echo csrf_field(); ?>
                        </form>
                    </div>
                </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav><?php /**PATH C:\xampp\htdocs\autobazaar\resources\views/admin/include/header.blade.php ENDPATH**/ ?>