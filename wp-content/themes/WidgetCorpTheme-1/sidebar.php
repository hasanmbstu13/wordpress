
    <aside>

        <?php get_search_form(); ?>
        
        <nav>
           <?php wp_nav_menu(array('menu' => 'Main Nav Menu')); ?>
        </nav>

        <div class="widget latest-post">

            <h4>Latest Post</h4>

            <div class="sidebar-post">
                <p class="date">March 10, 2010</p>
                <h5>This just in: Don't get Gremlins Wet!</h5>
                <p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante.</p>
            </div>

        </div> <!-- END Latest Posts -->

        <div class="widget industry-news">

            <h4>Industry News</h4>

            <div class="sidebar-post">
                <p class="date">March 11, 2010</p>
                <h5>Widgets are the new Sprockets</h5>
                <p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante.</p>
            </div>

            <div class="sidebar-post">
                <p class="date">March 03, 2010</p>
                <h5>Fifth Birthday of the Intersprockletometer</h5>
                <p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper.</p>
            </div>

        </div> <!-- END Industry News -->
    <?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('Sidebar Widgets')) : else : ?>
        <!-- All this stuff in here only shows up if you DON'T have any widgets active in this zone -->
    <?php endif; ?>
    </aside>