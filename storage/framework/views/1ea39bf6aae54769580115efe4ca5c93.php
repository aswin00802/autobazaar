<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo">
        <a href="index.html" class="app-brand-link">
            <span class="app-brand-logo demo">
                <?php if(getSetting('web_logo')): ?>
                    <img src="<?php echo e(asset(getSetting('web_logo'))); ?>" class="img-fluid" style="width: 40px;height:40px">
                <?php else: ?>
                    <img src="<?php echo e(asset('admin/logo/logo.png')); ?>" class="img-fluid w-25">
                <?php endif; ?>
            </span>
            <span class="app-brand-text demo menu-text fw-semibold ms-2"><?php echo e(getSetting('business_name')); ?></span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M8.47365 11.7183C8.11707 12.0749 8.11707 12.6531 8.47365 13.0097L12.071 16.607C12.4615 16.9975 12.4615 17.6305 12.071 18.021C11.6805 18.4115 11.0475 18.4115 10.657 18.021L5.83009 13.1941C5.37164 12.7356 5.37164 11.9924 5.83009 11.5339L10.657 6.707C11.0475 6.31653 11.6805 6.31653 12.071 6.707C12.4615 7.09747 12.4615 7.73053 12.071 8.121L8.47365 11.7183Z"
                fill-opacity="0.9" />
            <path
                d="M14.3584 11.8336C14.0654 12.1266 14.0654 12.6014 14.3584 12.8944L18.071 16.607C18.4615 16.9975 18.4615 17.6305 18.071 18.021C17.6805 18.4115 17.0475 18.4115 16.657 18.021L11.6819 13.0459C11.3053 12.6693 11.3053 12.0587 11.6819 11.6821L16.657 6.707C17.0475 6.31653 17.6805 6.31653 18.071 6.707C18.4615 7.09747 18.4615 7.73053 18.071 8.121L14.3584 11.8336Z"
                fill-opacity="0.4" />
            </svg>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('dashboard')): ?>
        <li class="menu-item <?php echo e(areActiveRoutes(['dashboard'])); ?>">
            <a href="<?php echo e(route('dashboard')); ?>" class="menu-link">
                <i class="menu-icon icon-base ri ri-home-smile-line"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['country','state','city','auto_brand','auto_brand_model','auto_fuel_type','auto_seller','auto_finance','authorized_seller'])): ?>
        <li class="menu-item <?php echo e(areActiveRoutesList(['masters.country','masters.country.create','masters.country.edit','masters.state','masters.state.create','masters.state.edit','masters.city','masters.city.create','masters.city.edit','masters.auto-brands','masters.auto-brands.create','masters.auto-brands.edit','masters.auto-brands.model','masters.auto-brands.model.create','masters.auto-brands.model.edit','masters.auto-fueltype','masters.auto-fueltype.create','masters.auto-fueltype.edit','masters.auto-seller','masters.auto-seller.create','masters.auto-seller.edit','masters.auto-finance','masters.auto-finance.create','masters.auto-finance.edit','masters.auto-authorized-seller','masters.auto-authorized-seller.create','masters.auto-authorized-seller.edit'])); ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ri ri-list-check-2"></i>
                <div data-i18n="Masters">Masters</div>
            </a>
            <ul class="menu-sub">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('auto_brand')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['masters.auto-brands','masters.auto-brands.create','masters.auto-brands.edit'])); ?>">
                    <a href="<?php echo e(route('masters.auto-brands')); ?>" class="menu-link">
                    <div data-i18n="Brands">Brands</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('auto_brand_model')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['masters.auto-brands.model','masters.auto-brands.model.create','masters.auto-brands.model.edit'])); ?>">
                    <a href="<?php echo e(route('masters.auto-brands.model')); ?>" class="menu-link">
                    <div data-i18n="Models">Models</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('auto_fuel_type')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['masters.auto-fueltype','masters.auto-fueltype.create','masters.auto-fueltype.edit'])); ?>">
                    <a href="<?php echo e(route('masters.auto-fueltype')); ?>" class="menu-link">
                    <div data-i18n="Fuel Type">Fuel Type</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('auto_seller')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['masters.auto-seller','masters.auto-seller.create','masters.auto-seller.edit'])); ?>">
                    <a href="<?php echo e(route('masters.auto-seller')); ?>" class="menu-link">
                    <div data-i18n="Auto Sellers">Used Auto Sellers</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('authorized_seller')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['masters.auto-authorized-seller','masters.auto-authorized-seller.create','masters.auto-authorized-seller.edit'])); ?>">
                    <a href="<?php echo e(route('masters.auto-authorized-seller')); ?>" class="menu-link">
                    <div data-i18n="authorized-sellers">Authorized Sellers</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('auto_finance')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['masters.auto-finance','masters.auto-finance.create','masters.auto-finance.edit'])); ?>">
                    <a href="<?php echo e(route('masters.auto-finance')); ?>" class="menu-link">
                    <div data-i18n="Finance">Finance Partners</div>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('country')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['masters.country','masters.country.create','masters.country.edit'])); ?>">
                    <a href="<?php echo e(route('masters.country')); ?>" class="menu-link">
                    <div data-i18n="Country">Country</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('state')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['masters.state','masters.state.create','masters.state.edit'])); ?>">
                    <a href="<?php echo e(route('masters.state')); ?>" class="menu-link">
                    <div data-i18n="State">State</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('city')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['masters.city','masters.city.create','masters.city.edit'])); ?>">
                    <a href="<?php echo e(route('masters.city')); ?>" class="menu-link">
                    <div data-i18n="City">City</div>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('events_announce')): ?>
        <li class="menu-item <?php echo e(areActiveRoutes(['events','events.create','events.edit'])); ?>">
            <a href="<?php echo e(route('events')); ?>" class="menu-link">
                <i class="menu-icon icon-base ri ri-calendar-event-line"></i>
                <div data-i18n="Events">Events</div>
            </a>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['roles','permissions','role_has_permission'])): ?>
        <li class="menu-item <?php echo e(areActiveRoutesList(['roles','roles.create','roles.edit','permissions','permissions.create','permissions.edit','role_has_permission','role_has_permission.edit'])); ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ri ri-lock-2-line"></i>
                <div data-i18n="Roles & Permissions">Roles & Permissions</div>
            </a>
            <ul class="menu-sub">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('roles')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['roles','roles.create','roles.edit'])); ?>">
                    <a href="<?php echo e(route('roles')); ?>" class="menu-link">
                    <div data-i18n="Role">Role</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('permissions')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['permissions', 'permissions.create', 'permissions.edit'], 'active')); ?>">
                    <a href="<?php echo e(route('permissions')); ?>" class="menu-link">
                    <div data-i18n="Permissions">Permissions</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('role_has_permission')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['role_has_permission', 'role_has_permission.edit'], 'active')); ?>">
                    <a href="<?php echo e(route('role_has_permission')); ?>" class="menu-link">
                    <div data-i18n="Role has Permission">Role has Permission</div>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['used_auto','new_auto','private_cargo_auto','bajaj_refinance_auto'])): ?>
        <li class="menu-item <?php echo e(areActiveRoutesList(['auto-management.used-auto','auto-management.used-auto.create','auto-management.used-auto.edit','auto-management.used-auto.view-details','auto-management.private-cargo-auto','auto-management.private-cargo-auto.create','auto-management.private-cargo-auto.edit','auto-management.private-cargo-auto.view-details','auto-management.bajaj-refinance-auto','auto-management.bajaj-refinance-auto.create','auto-management.bajaj-refinance-auto.edit','auto-management.bajaj-refinance-auto.view-details','auto-management.new-auto','auto-management.new-auto.create','auto-management.new-auto.edit','auto-management.new-auto.view-details'])); ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ri ri-truck-line"></i>
                <div data-i18n="Auto Management">Auto Management</div>
            </a>
            <ul class="menu-sub">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('used_auto')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['auto-management.used-auto','auto-management.used-auto.create','auto-management.used-auto.edit','auto-management.used-auto.view-details'])); ?>">
                    <a href="<?php echo e(route('auto-management.used-auto')); ?>" class="menu-link">
                    <div data-i18n="Used Auto">Used Auto</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('new_auto')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['auto-management.new-auto','auto-management.new-auto.create','auto-management.new-auto.edit','auto-management.new-auto.view-details'])); ?>">
                    <a href="<?php echo e(route('auto-management.new-auto')); ?>" class="menu-link">
                    <div data-i18n="New Auto">New Auto</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('private_cargo_auto')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['auto-management.private-cargo-auto','auto-management.private-cargo-auto.create','auto-management.private-cargo-auto.edit','auto-management.private-cargo-auto.view-details'])); ?>">
                    <a href="<?php echo e(route('auto-management.private-cargo-auto')); ?>" class="menu-link">
                    <div data-i18n="Private Cargo">Private Cargo</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('bajaj_refinance_auto')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['auto-management.bajaj-refinance-auto','auto-management.bajaj-refinance-auto.create','auto-management.bajaj-refinance-auto.edit','auto-management.bajaj-refinance-auto.view-details'])); ?>">
                    <a href="<?php echo e(route('auto-management.bajaj-refinance-auto')); ?>" class="menu-link">
                    <div data-i18n="Bajaj Re-Finance">Bajaj Re-Finance</div>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solid_autos_list')): ?>
        <li class="menu-item <?php echo e(areActiveRoutes(['sold-auto.list'])); ?>">
            <a href="<?php echo e(route('sold-auto.list')); ?>" class="menu-link">
                <i class="menu-icon icon-base ri ri-tools-fill"></i>
                <div data-i18n="Sold Autos">Sold Autos</div>
            </a>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['user_list'])): ?>
        <li class="menu-item <?php echo e(areActiveRoutesList(['user-management.users-list','user-management.users-info'])); ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ri ri-user-settings-fill"></i>
                <div data-i18n="User Management">User Management</div>
            </a>
            <ul class="menu-sub">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user_list')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['user-management.users-list','user-management.users-info'])); ?>">
                    <a href="<?php echo e(route('user-management.users-list')); ?>" class="menu-link">
                    <div data-i18n="Users">Users</div>
                    </a>
                </li>
                <?php endif; ?>
                <!-- <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user_post_auto_list')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['user-management.users-post-auto-list'])); ?>">
                    <a href="<?php echo e(route('user-management.users-post-auto-list')); ?>" class="menu-link">
                    <div data-i18n="User Post Autos">User Post Autos</div>
                    </a>
                </li>
                <?php endif; ?> -->
            </ul>
        </li>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['user_post_auto_list'])): ?>
        <li class="menu-item <?php echo e(areActiveRoutesList(['user-management.users-post-auto-list'])); ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ri ri-creative-commons-by-line"></i>
                <div data-i18n="Admin Approval">Admin Approval</div>
            </a>
            <ul class="menu-sub">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('user_post_auto_list')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['user-management.users-post-auto-list'])); ?>">
                    <a href="<?php echo e(route('user-management.users-post-auto-list')); ?>" class="menu-link">
                    <div data-i18n="User Post Autos">User Post Autos</div>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['ecommerce_orders','ecommerce_coupons','sparepart_categories','sparepart_subcategories','sparepart_product','sparepart_pendingorders','sparepart_successorders','sparepart_cancelorders'])): ?>
        
        <li class="menu-item <?php echo e(areActiveRoutesList(['ecommerce.orders','ecommerce.orders.status','ecommerce.orders.view','ecommerce.coupons','ecommerce.coupons.create','ecommerce.coupons.edit','spare-parts.categories','spare-parts.categories.create','spare-parts.categories.edit','spare-parts.sub-categories','spare-parts.sub-categories.create','spare-parts.sub-categories.edit','spare-parts.product','spare-parts.product.create','spare-parts.product.edit','spare-parts.orders.pending','spare-parts.orders.success','spare-parts.orders.cancel'])); ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ri ri-shopping-cart-2-line"></i>
                <div data-i18n="E-commerce">E-commerce</div>
            </a>
            <ul class="menu-sub">

                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ecommerce_orders')): ?>
                <li class="menu-item <?php echo e(areActiveRoutesList(['ecommerce.orders','ecommerce.orders.status','ecommerce.orders.view'])); ?>">
                    <a href="<?php echo e(route('ecommerce.orders')); ?>" class="menu-link">
                        <div data-i18n="Orders">Orders</div>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ecommerce_coupons')): ?>
                <li class="menu-item <?php echo e(areActiveRoutesList(['ecommerce.coupons','ecommerce.coupons.create','ecommerce.coupons.edit'])); ?>">
                    <a href="<?php echo e(route('ecommerce.coupons')); ?>" class="menu-link">
                        <div data-i18n="Coupons">Coupons</div>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sparepart_categories')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['spare-parts.categories','spare-parts.categories.create','spare-parts.categories.edit'])); ?>">
                    <a href="<?php echo e(route('spare-parts.categories')); ?>" class="menu-link">
                        <div data-i18n="Categories">Categories</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sparepart_subcategories')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['spare-parts.sub-categories','spare-parts.sub-categories.create','spare-parts.sub-categories.edit'])); ?>">
                    <a href="<?php echo e(route('spare-parts.sub-categories')); ?>" class="menu-link">
                        <div data-i18n="Sub Categories">Sub Categories</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sparepart_product')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['spare-parts.product','spare-parts.product.create','spare-parts.product.edit'])); ?>">
                    <a href="<?php echo e(route('spare-parts.product')); ?>" class="menu-link">
                        <div data-i18n="Products">Products</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['sparepart_pendingorders','sparepart_successorders','sparepart_cancelorders'])): ?>
                <li class="menu-item <?php echo e(areActiveRoutesList(['spare-parts.orders.pending','spare-parts.orders.success','spare-parts.orders.cancel'])); ?>">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div data-i18n="Legacy Orders">Legacy Orders</div>
                    </a>
                    <ul class="menu-sub">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sparepart_pendingorders')): ?>
                        <li class="menu-item <?php echo e(areActiveRoutes(['spare-parts.orders.pending'])); ?>">
                            <a href="<?php echo e(route('spare-parts.orders.pending')); ?>" class="menu-link">
                                <div data-i18n="Pending Orders">Pending Orders</div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sparepart_successorders')): ?>
                        <li class="menu-item <?php echo e(areActiveRoutes(['spare-parts.orders.success'])); ?>">
                            <a href="<?php echo e(route('spare-parts.orders.success')); ?>" class="menu-link">
                                <div data-i18n="Success Orders">Success Orders</div>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sparepart_cancelorders')): ?>
                        <li class="menu-item <?php echo e(areActiveRoutes(['spare-parts.orders.cancel'])); ?>">
                            <a href="<?php echo e(route('spare-parts.orders.cancel')); ?>" class="menu-link">
                                <div data-i18n="Cancel Orders">Cancel Orders</div>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pos_quotation')): ?>
        <li class="menu-item <?php echo e(areActiveRoutes(['pos-quotation','pos-quotation.create'])); ?>">
            <a href="<?php echo e(route('pos-quotation')); ?>" class="menu-link">
                <i class="menu-icon icon-base ri ri-calculator-line"></i>
                <div data-i18n="POS Quotation">POS Quotation</div>
            </a>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('auto_enquiry_list')): ?>
        <li class="menu-item <?php echo e(areActiveRoutes(['enquiry-auto.list'])); ?>">
            <a href="<?php echo e(route('enquiry-auto.list')); ?>" class="menu-link">
                <i class="menu-icon icon-base ri ri-user-search-fill"></i>
                <div data-i18n="Enquiry Details">Enquiry Details</div>
            </a>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('quotation')): ?>
        <li class="menu-item <?php echo e(areActiveRoutes(['quotation-list'])); ?>">
            <a href="<?php echo e(route('quotation-list')); ?>" class="menu-link">
                <i class="menu-icon icon-base ri ri-user-search-fill"></i>
                <div data-i18n="Quotation Details">Quotation Details</div>
            </a>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('auto_meter')): ?>
        <li class="menu-item <?php echo e(areActiveRoutes(['auto-meter'])); ?>">
            <a href="<?php echo e(route('auto-meter')); ?>" class="menu-link">
                <i class="menu-icon icon-base ri ri-funds-line"></i>
                <div data-i18n="Auto Meter">Auto Meter</div>
            </a>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('auto_emergency_list')): ?>
        <li class="menu-item <?php echo e(areActiveRoutes(['emergency.request.list'])); ?>">
            <a href="<?php echo e(route('emergency.request.list')); ?>" class="menu-link">
                <i class="menu-icon icon-base ri ri-alert-fill"></i>
                <div data-i18n="Emergency Request">Emergency Request</div>
            </a>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('auto_driver_list')): ?>
        <li class="menu-item <?php echo e(areActiveRoutes(['driver.request.list'])); ?>">
            <a href="<?php echo e(route('driver.request.list')); ?>" class="menu-link">
                <i class="menu-icon icon-base ri ri-user-shared-2-fill"></i>
                <div data-i18n="Driver Requests">Driver Requests</div>
            </a>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['fairprice_ride_list','fairprice_fare_setting'])): ?>
        <li class="menu-item <?php echo e(areActiveRoutesList(['fairprice.rides','fairprice.rides.show','fairprice.fare-settings'])); ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ri ri-taxi-line"></i>
                <div data-i18n="FairPrice">FairPrice</div>
            </a>
            <ul class="menu-sub">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('fairprice_ride_list')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['fairprice.rides','fairprice.rides.show'])); ?>">
                    <a href="<?php echo e(route('fairprice.rides')); ?>" class="menu-link">
                        <div data-i18n="Ride Requests">Ride Requests</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('fairprice_fare_setting')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['fairprice.fare-settings'])); ?>">
                    <a href="<?php echo e(route('fairprice.fare-settings')); ?>" class="menu-link">
                        <div data-i18n="Fare Settings">Fare Settings</div>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['gas_station','mechanic','insurance','re_finance','rto'])): ?>
        <li class="menu-item <?php echo e(areActiveRoutesList(['services.gas-station','services.gas-station.create','services.gas-station.edit','services.mechanic','services.mechanic.create','services.mechanic.edit','services.insurance','services.re-finance','services.rto'])); ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ri ri-list-settings-fill"></i>
                <div data-i18n="Services">Services</div>
            </a>
            <ul class="menu-sub">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('gas_station')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['services.gas-station','services.gas-station.create','services.gas-station.edit'])); ?>">
                    <a href="<?php echo e(route('services.gas-station')); ?>" class="menu-link">
                    <div data-i18n="Gas Station">Gas Station</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('mechanic')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['services.mechanic','services.mechanic.create','services.mechanic.edit'])); ?>">
                    <a href="<?php echo e(route('services.mechanic')); ?>" class="menu-link">
                    <div data-i18n="Mechanic">Mechanic</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('insurance')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['services.insurance'])); ?>">
                    <a href="<?php echo e(route('services.insurance')); ?>" class="menu-link">
                    <div data-i18n="Insurance">Insurance</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('re_finance')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['services.re-finance'])); ?>">
                    <a href="<?php echo e(route('services.re-finance')); ?>" class="menu-link">
                    <div data-i18n="Re Finance">Re Finance</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('rto')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['services.rto'])); ?>">
                    <a href="<?php echo e(route('services.rto')); ?>" class="menu-link">
                    <div data-i18n="RTO Requests">RTO Requests</div>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['general_setting','payment_setting','smtp_setting','emailtemplate_setting'])): ?>
        <li class="menu-item <?php echo e(areActiveRoutesList(['settings.general-settings','settings.payment-settings','settings.smtp-settings','settings.email-template-settings','settings.email-template-settings.create','settings.email-template-settings.edit'])); ?> }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ri ri-settings-5-line"></i>
                <div data-i18n="Business Settings">Business Settings</div>
            </a>
            <ul class="menu-sub">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('general_setting')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['settings.general-settings'])); ?>">
                    <a href="<?php echo e(route('settings.general-settings')); ?>" class="menu-link">
                    <div data-i18n="General Setting">General Setting</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('payment_setting')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['settings.payment-settings'])); ?>">
                    <a href="<?php echo e(route('settings.payment-settings')); ?>" class="menu-link">
                    <div data-i18n="Payments Setting">Payments Setting</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('smtp_setting')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['settings.smtp-settings'])); ?>">
                    <a href="<?php echo e(route('settings.smtp-settings')); ?>" class="menu-link">
                    <div data-i18n="SMTP Setting">SMTP Setting</div>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('emailtemplate_setting')): ?>
                <li class="menu-item <?php echo e(areActiveRoutes(['settings.email-template-settings','settings.email-template-settings.create','settings.email-template-settings.edit'])); ?>">
                    <a href="<?php echo e(route('settings.email-template-settings')); ?>" class="menu-link">
                    <div data-i18n="Email Setting">Email Setting</div>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

    </ul>
</aside>

<div class="menu-mobile-toggler d-xl-none rounded-1">
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
    <i class="ri ri-menu-line icon-base"></i>
    <i class="ri ri-arrow-right-s-line icon-base"></i>
    </a>
</div>
<?php /**PATH C:\xampp\htdocs\autobazaar\resources\views/admin/include/sidenav.blade.php ENDPATH**/ ?>