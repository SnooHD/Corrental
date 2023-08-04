<div id="apus-mobile-menu" class="apus-offcanvas d-block d-xl-none"> 
    <div class="apus-offcanvas-body">
            <div class="header-offcanvas">
                <div class="clearfix">
                    <div class="d-flex align-items-center">
                        <?php
                            $logo_url = nerf_get_config('media-mobile-logo');
                        ?>
                        <?php if( !empty($logo_url) ): ?>
                            <div class="logo">
                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                                    <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>">
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="logo logo-theme">
                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                                    <img src="<?php echo esc_url( get_template_directory_uri().'/images/logo.svg'); ?>" alt="<?php bloginfo( 'name' ); ?>">
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="ms-auto">
                            <a class="btn-showmenu">
                                <i class="ti-close"></i><span class="title"><?php echo esc_html__('MENU','nerf'); ?></span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <div class="offcanvas-content">
                <div class="middle-offcanvas">

                    <nav id="menu-main-menu-navbar" class="navbar navbar-offcanvas" role="navigation">
                        <?php
                            $mobile_menu = 'primary';
                            $menus = get_nav_menu_locations();
                            if( !empty($menus['mobile-primary']) && wp_get_nav_menu_items($menus['mobile-primary'])) {
                                $mobile_menu = 'mobile-primary';
                            }
                            $args = array(
                                'theme_location' => $mobile_menu,
                                'container_class' => 'navbar-collapse navbar-offcanvas-collapse',
                                'menu_class' => 'nav navbar-nav main-mobile-menu',
                                'fallback_cb' => '',
                                'menu_id' => '',
                                'container' => 'div',
                                'walker' => new Nerf_Mobile_Menu()
                            );
                            wp_nav_menu($args);

                        ?>
                    </nav>

                </div>
            </div>
    </div>
</div>
<div class="over-dark"></div>