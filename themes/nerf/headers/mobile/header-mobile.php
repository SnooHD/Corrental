<div id="apus-header-mobile" class="header-mobile d-block d-xl-none clearfix">   
    <div class="container">
            <div class="row d-flex align-items-center">
                <div class="col-7">
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
                                <img src="<?php echo esc_url( get_template_directory_uri().'/images/logo-white.svg'); ?>" alt="<?php bloginfo( 'name' ); ?>">
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-5 d-flex align-items-center justify-content-end">

                        <?php if ( nerf_get_config('header_mobile_menu', true) ) { ?>
                            <a href="#navbar-offcanvas" class="btn-showmenu d-inline-flex align-items-center">
                                <i class="vertical-icon"></i>
                                <span class="title"><?php echo esc_html__('MENU','nerf'); ?></span>
                            </a>
                        <?php } ?>
                </div>
            </div>
    </div>
</div>