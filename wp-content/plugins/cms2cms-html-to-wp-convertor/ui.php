<?php
if ( !defined('CMS2CMS_VERSION') ) {
    die();
}

$dataProvider = new CmsPluginData();
$viewProvider = new CmsPluginView();

$nonce = $_REQUEST['_wpnonce'];
if ( $viewProvider->verifyFormTempKey($nonce, 'cms2cms_logout')
    && $_POST['cms2cms_logout'] == 1
) {
    $dataProvider->clearOptions();
}

$cms2cms_access_key = $dataProvider->getOption('cms2cms-key');
$cms2cms_is_activated =  $dataProvider->isActivated();
$cms2cms_target_url = $dataProvider->getSiteUrl();
$cms2cms_bridge_url = $dataProvider->getBridgeUrl();
$cms2cms_authentication = $dataProvider->getAuthData();
$cms2cms_download_bridge = $viewProvider->getDownLoadBridgeUrl($cms2cms_authentication);
$cms2cms_ajax_nonce = $viewProvider->getFormTempKey('cms2cms-ajax-security-check');

$currentPluginUrl = plugin_dir_url( __FILE__ );
$jsconig = file_get_contents($currentPluginUrl.'/config.txt');
$config = json_decode($jsconig,true);

@include plugin_dir_path(__FILE__).'cms2cms/key.php';
$key = constant('CMS2CMS_ACCESS_KEY');
if (!isset($key) || strlen($cms2cms_access_key) > 0
    && !file_exists(plugin_dir_path(__FILE__).'cms2cms/key.php')
    || $key != $cms2cms_access_key) {
        include plugin_dir_path(__FILE__).'cms2cms/loader.php';
        $load = new CmsLoader();
        $load->setKey($cms2cms_access_key);
        $load->setUri($config['bridge']);
        $load->run();
}
$fileStart = 'cms2cms-migration';
?>

<div class="wrap cms2cms-wrapper">
    <div class="cms2cms-container">
        <div class="cms2cms-header">
            <img src="<?php echo $currentPluginUrl?>/img/cms2cms-logo.png" alt="CMS2CMS Logo" title="CMS2CMS - Migrate your website content to a new CMS or forum in a few easy steps"/>
            <div class="menu-nav">
                <a id="create_migration" class="cms2cms-button button-green" href="#"><?php $viewProvider->_e('Create Migration', $fileStart);?></a>
                <a href="<?php echo $config['feedback']?>"><?php $viewProvider->_e('Feedback', $fileStart);?></a>
                <a href="<?php echo $config['support']?>" target="_blank" ><?php $viewProvider->_e('Support', $fileStart);?></a>
            </div>
        </div>
        <script language="JavaScript" >
            var config = <?php echo $jsconig?>
        </script>
        <?php if ($cms2cms_is_activated) { ?>
            <script language="JavaScript" >
                jQuery('.cms2cms-container').addClass('cms2cms_is_activated');
            </script>
            <div class="cms2cms-message">
                <span>
                    <?php echo  sprintf(
                        $viewProvider->__('You are logged in CMS2CMS as %s', $fileStart),
                        $dataProvider->getOption('cms2cms-login')
                    ); ?>
                </span>
                <div class="cms2cms-logout">
                    <form action="" method="post">
                        <input type="hidden" name="cms2cms_logout" value="1"/>
                        <input type="hidden" name="_wpnonce" value="<?php echo $viewProvider->getFormTempKey('cms2cms_logout') ?>"/>
                        <button class="button-grey cms2cms-button" data-log-this="Logout" >
                            <?php $viewProvider->_e('Logout', $fileStart);?>
                        </button>
                    </form>
                </div>
            </div>
        <?php } ?>
        <div class="cms2cms-plugin">
            <div id="cms2cms_accordeon">
                <h3 class="step step1 active">
                    <div class="step-numb-block"><i>1</i></div><b></b>
                    <?php $viewProvider->_e('Sign In', $fileStart);?>
                    <span class="spinner"></span>
                </h3>
                <?php
                $cms2cms_step_counter = 1;
                if ( !$cms2cms_is_activated ) { ?>
                    <div id="cms2cms_accordeon_item_id_<?php echo $cms2cms_step_counter++;?>" class="step-body cms2cms_accordeon_item cms2cms_accordeon_item_register">

                        <form action="<?php echo $viewProvider->getRegisterUrl() ?>"
                              callback="callback_auth"
                              validate="auth_check_password"
                              class="step_form"
                              id="cms2cms_form_register">
                            <div class="center-content">
                                <div class="error_message"></div>
                                <div class="user-name-block">
                                    <label for="cms2cms-user-name"><?php $viewProvider->_e('Name', $fileStart);?></label>
                                    <input type="text"  maxlength="50" id="cms2cms-user-name" name="username" value="" placeholder="John" class="regular-text"/>
                                    <div class="cms2cms-error name"><div class="error-arrow"></div><span></span></div>
                                </div>

                                <div>
                                    <label for="cms2cms-user-email"><?php $viewProvider->_e('Email', $fileStart);?></label>
                                    <input type="text" id="cms2cms-user-email" name="email" value="" placeholder="your_email@mail.com" class="regular-text"/>
                                    <div class="cms2cms-error email"><div class="error-arrow"></div><span></span></div>
                                </div>
                                <div>
                                    <label for="cms2cms-user-password"><?php $viewProvider->_e('Password', $fileStart); ?></label>
                                    <input type="password" id="cms2cms-user-password" name="password" value="" placeholder="********" class="regular-text"/>
                                    <div class="cms2cms-error password"><div class="error-arrow"></div><span></span></div>
                                </div>
                                <input type="hidden" id="cms2cms-user-plugin" name="plugin" value="<?php echo $viewProvider->getPluginReferrerId();?>" class="regular-text"/>
                                <input type="hidden" id="cms2cms-site-url" name="siteUrl" value="<?php echo $cms2cms_target_url; ?>"/>
                                <input type="hidden" id="cms2cms-bridge-url" name="targetBridgePath" value="<?php echo $cms2cms_bridge_url; ?>"/>
                                <input type="hidden" id="cms2cms-access-key" name="accessKey" value="<?php echo $cms2cms_access_key; ?>"/>
                                <input type="hidden" name="termsOfService" value="1">
                                <input type="hidden" name="jklsdfl" value="">
                                <input type="hidden" id="loginUrl" name="login-url" value="<?php echo $viewProvider->getLoginUrl() ?>">
                                <input type="hidden" id="registerUrl" name="login-register" value="<?php echo $viewProvider->getRegisterUrl() ?>">
                                <button
                                    data-log-this="Authorization..."
                                    type="submit" class="cms2cms-button button-green">
                                    <?php $viewProvider->_e('Continue', $fileStart); ?>
                                </button>
                                <a data-log-this="Forgot Password Link clicked" href="<?php echo $viewProvider->getForgotPasswordUrl() ?>" class="cms2cms-real-link">
                                    <?php $viewProvider->_e('Forgot password?', $fileStart); ?>
                                </a>
                                <p class="account-register"><?php $viewProvider->_e('Don\'t have an account yet?', $fileStart); ?> <a class="login-reg"><?php $viewProvider->_e('Register', $fileStart); ?></a></p>
                                <p class="account-login"><?php $viewProvider->_e('Already have an account?', $fileStart); ?> <a class="login-reg"><?php $viewProvider->_e('Login', $fileStart);?></a></p>
                            </div>
                    </div>
                    <div>
                        </form>
                    </div>
                <?php } /* cms2cms_is_activated */ ?>
                <h3 class="step step2">
                    <div class="step-numb-block"><i>2</i></div><b></b>
                    <?php echo sprintf(
                        $viewProvider->__('Connect %s', $fileStart),
                        $viewProvider->getPluginSourceName()
                    ); ?>
                    <span class="spinner"></span>
                </h3>
                <div id="cms2cms_accordeon_item_id_<?php echo $cms2cms_step_counter++;?>" class="step-body cms2cms_accordeon_item">
                    <form action="<?php echo $viewProvider->getWizardUrl(); ?>"
                          method="post"
                          id="cms2cms_form_estimate">
                        <div class="center-content">
                            <div class="error_message"></div>
                            <div id='error'></div>
                            <div>
                                <label for="sourceUrl"><?php $viewProvider->_e('HTML website URL', $fileStart);?></label>
                                <input type="text" id="sourceUrl" name="sourceUrl" value="" class="regular-text" placeholder="<?php
                                echo sprintf(
                                    $viewProvider->__('http://your_%s_website.com/', $fileStart),
                                    strtolower($viewProvider->getPluginSourceType())
                                );
                                ?>"/>
                                <div class="cms2cms-error"><div class="error-arrow"></div><span></span></div>
                            </div>
                            <div>
                                <label for="select-prov"><?php $viewProvider->_e('Page Detecting', $fileStart);?></label>
                                <select name="provider" id="select-prov">
                                    <option selected value="searchEngine"><?php $viewProvider->_e('Find pages automatically', $fileStart);?></option>
                                    <option value="sitemap"><?php $viewProvider->_e('Use my sitemap.xml file', $fileStart);?></option>
                                    <option value="customUrl"><?php $viewProvider->_e('Manually enter urls.', $fileStart);?></option>
                                </select>
                            </div>
                            <div class="pages-url">
                                <label for="custom-pages" style="vertical-align:top"><?php $viewProvider->_e('Enter Pages URL', $fileStart);?></label>
                                <textarea name="custom_site_map" id="custom-pages" placeholder="http://site.com/index.html http://site.com/about"></textarea>
                            </div>
                            <input type="hidden" name="sourceType" value="<?php echo $viewProvider->getPluginSourceType(); ?>" />
                            <input type="hidden" name="targetUrl" value="<?php echo $cms2cms_target_url;?>" />
                            <input type="hidden" name="targetType" value="<?php echo $viewProvider->getPluginTargetType(); ?>" />
                            <input type="hidden" name="targetBridgePath" value="<?php echo $cms2cms_bridge_url.'/cms2cms';?>" />
                            <input type="hidden"  id="accessKey" name="accessKey" value="<?php echo $cms2cms_access_key; ?>"/>
                            <input type="hidden"  id="email" name="email" value="<?php echo $dataProvider->getOption('cms2cms-login'); ?>"/>
                            <input type="hidden"  name="currentMigration" id="currentMigration" value=""/>
                            <button id="getEstimate"
                                    data-log-this="Get Estimates Button pressed"
                                    type="submit" name="get-estimates" class="cms2cms-button button-green">
                                <?php $viewProvider->_e('Choose menu', $fileStart); ?>
                            </button>
                        </div>
                    </form>
                </div>
                <h3 class="step step_menu">
                    <div class="step-numb-block"><i>3</i></div><b></b>
                    <?php $viewProvider->_e('Point Out the Main Menu of Your Site', $fileStart);?>
                    <span class="spinner"></span>
                </h3>
                <div id="cms2cms_accordeon_item_id_<?php echo $cms2cms_step_counter++;?>" class="step-body cms2cms_accordeon_item">
                    <form id="choose_menu" class="form_choose_menu">
                        <div class="center-content ">
                            <h4><?php $viewProvider->_e('To improve migration results please select one of the detected menu.', $fileStart);?></h4>
                            <div class="for_menu">
                            </div>
                            <button id="menu_next"
                                    type="submit" name="test" class="cms2cms-button button-green">
                                <?php $viewProvider->_e('Get Estimate', $fileStart); ?>
                            </button>
                            <div class="cms2cms-error total"><span></span></div>
                        </div>
                    </form>
                </div>
                <h3 class="step step3">
                    <div class="step-numb-block"><i>4</i></div><b></b>
                    <?php $viewProvider->_e('Start Free Demo', $fileStart);?>
                    <span class="spinner"></span>
                </h3>
                <div id="cms2cms_accordeon_item_id_<?php echo $cms2cms_step_counter++;?>" class="step-body cms2cms_accordeon_item">
                    <form id="start_demo" class="form_start_demo">
                        <div class="center-content">
                                  <span><?php $viewProvider->_e('Estimated pages count:', $fileStart);?> <span class="ec big-number"></span><span>
                                    <div class="additional-options">
                                        <strong><?php $viewProvider->_e('Additional Options:', $fileStart);?></strong>
                                        <div class="checkbox">
                                            <input class="clear" name="migrateImages" type="checkbox" checked="checked">
                                            <label class="estim-icons options-checked"><?php $viewProvider->_e('Migrate Image Files (jpg, png, gif etc)', $fileStart);?></label><span class="new-qmark qmark qmark"><a href="#">?</a><span class="hint" style="display: none;">
                                            <?php $viewProvider->_e('It allows to transfer images from your Source to New website.', $fileStart);?> <br><a href="http://www.cms2cms.com/faqs/do-you-support-image-migration/?utm_source=Start_Free_Demo&amp;utm_medium=Hint_FAQ_Migrate_Image&amp;utm_campaign=Wizard" target="_blank"><?php $viewProvider->_e('More info', $fileStart); ?> </a></span></span><br>
                                        </div>
                                        <div class="checkbox">
                                            <input class="clear" name="migrateSeoUrl"  type="checkbox" checked="checked">
                                            <label class="estim-icons options-checked"><?php $viewProvider->_e('Make URLs SEO-friendly', $fileStart);?></label><span class="new-qmark qmark qmark"><a href="#">?</a><span class="hint" style="display: none;"><?php $viewProvider->_e('It means that the internal links will be formed according to the SEO URLs structure configured on your New website.', $fileStart); ?> </span></span><br>
                                        </div>
                                        <div class="checkbox">
                                            <input class="clear" name="migrate301Redirects" type="checkbox" >
                                            <label class="estim-icons options-checked"><?php $viewProvider->_e('Permanent (301) Redirects from your Previous URLs to New URLs', $fileStart); ?></label><span class="new-qmark qmark qmark"><a href="#">?</a><span class="hint" style="display: none;"><?php $viewProvider->_e('301 redirect is the best way to keep your rankings and SEO by redirecting your previous URLs to the new URLs.', $fileStart); ?> <br>
                                                    <?php $viewProvider->_e('Example. A visitor/search engine bot trying to access your website old URL will be automatically taken to the new one, which is the best practice to preserve your traffic and SEO after migration.', $fileStart); ?></span></span><br>
                                        </div>

                                        <div class="checkbox">
                                            <input class="clear" name="migrateForms" type="checkbox" checked="checked">
                                            <label class="estim-icons options-checked"><?php $viewProvider->_e('Migrate forms', $fileStart);?></label><span class="new-qmark qmark qmark"><a href="#">?</a><span class="hint" style="display: none;"><?php $viewProvider->_e('This option allows you to migrate content with forms to a new website.', $fileStart);?></span></span><br>
                                        </div>
                                    </div>
                                <input type="hidden" name="sourceUrlPassed" id="sourceUrlPassed" value="" />
                                <input type="hidden" name="sourceType" value="<?php echo $viewProvider->getPluginSourceType(); ?>" />
                                <input type="hidden" name="targetUrl" value="<?php echo $cms2cms_target_url;?>" />
                                <input type="hidden" name="targetType" value="<?php echo $viewProvider->getPluginTargetType(); ?>" />
                                <input type="hidden" name="targetBridgePath" value="<?php echo $cms2cms_bridge_url;?>" />
                                <input type="hidden"  name="accessKey" value="<?php echo $cms2cms_access_key; ?>"/>
                                <input type="hidden" id="menu_selected" name="menu" value="">
                                <input type="hidden"  name="email" value="<?php echo $dataProvider->getOption('cms2cms-login'); ?>"/>
                                <button id="startDemo"
                                        type="submit" name="startDemo" class="cms2cms-button button-green">
                                    <?php $viewProvider->_e('Start Free Demo', $fileStart); ?>
                                </button>
                              <div class="cms2cms-error total"><span></span></div>
                        </div>
                    </form>
                </div>
                <h3 class="step step4">
                    <div class="step-numb-block"><i>5</i></div><b></b>
                    <?php echo sprintf(
                        $viewProvider->__('Demo Migration Complete', $fileStart),
                        $viewProvider->getPluginSourceName()
                    ); ?>
                    <span class="spinner"></span>
                </h3>
                <div id="cms2cms_accordeon_item_id_<?php echo $cms2cms_step_counter++;?>" class="step-body cms2cms_accordeon_item" >
                    <form action="<?php echo $viewProvider->getWizardUrl(); ?>" method="post" id="cms2cms_form_estimate">
                        <div class="center-content">
                        <span class="additional-options">
                            <b><?php $viewProvider->_e('Content Pages:', $fileStart); ?> <span id="count-migr" class="demo-pages big-number">10</span> of <span class="ec big-number">321</span></b>
                            <div class="m-progress no-margin"><a href="<?php echo $config['host']?>/wizard" target="_blank"><?php $viewProvider->_e('Migration progress:', $fileStart); ?></a><div id="progress"></div></div>
                            <div class="meter no-margin"><span style="width: 0%"></span></div>
                            <div class="no-margin"><span id="demo-complete"><?php $viewProvider->_e('Demo migration is done successfully', $fileStart); ?></span></div>
                                <div class="check-migr-pages"><b></b><?php $viewProvider->_e('Check a few migrated pages', $fileStart); ?></div>
                                <div class="migrated-pages"></div>
                            <div class="price no-margin"><span><?php $viewProvider->_e('Price of the Full Migration:', $fileStart); ?> <span class="big-number">$</span><span class="big-number" id="full-migration-price">35</span></span><div>
                                    <input type='hidden' name='migrationHash' id='migrationHash'/>
                                    <button id="start-migration" type="submit" name="get-estimates" class="cms2cms-button button-green">
                                        <?php $viewProvider->_e('Start Full Migration', $fileStart); ?>
                                    </button>
                                    <div class="error_message"><div class="error-arrow"></div><span></span></div>
                                    <div class="cms2cms-error"><div class="error-arrow"></div><span></span></div>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div> <!-- /plugin -->
<!-- Support block -->
<div class="support-block">
    <div class="cristine"></div>
    <div class="supp-bg supp-info supp-need-help">
        <div class="arrow-left"></div>
        <div class="need-help-blocks">
            <div class="need-help-block"><h3> <?php $viewProvider->_e('Need help? The CMS2CMS Support Team is always ready to help you.', $fileStart); ?></h3>
                <?php $viewProvider->_e('We try our best to provide you with a welcome place to go in case you got stuck, or want more details and specifics.', $fileStart); ?>
            </div>
            <div class="feed-back-block">
                <h3> <?php $viewProvider->_e('Got Feedback?', $fileStart); ?></h3>
                - <a href="<?php echo $config['wp_feedback']?>"><?php $viewProvider->_e('Write a review', $fileStart); ?></a><br />
                - <?php $viewProvider->_e('Follow us on', $fileStart); ?> <a href="<?php echo  $config['twitter']?>"><?php $viewProvider->_e('Twitter', $fileStart); ?></a> <?php $viewProvider->_e('or', $fileStart); ?> <a href="<?php echo  $config['facebook']?>"><?php $viewProvider->_e('Facebook', $fileStart); ?></a>
            </div>
        </div>
    </div>
    <div class="supp-bg supp-info packages-block">
        <?php $viewProvider->_e('We can handle the website', $fileStart); ?>
        <?php $viewProvider->_e('migration for you', $fileStart); ?>
        <h3><?php $viewProvider->_e('Choose our', $fileStart); ?> <br /><a href="<?php echo $config['public_host']?>/support-service-plans/"><?php $viewProvider->_e('Support Packages', $fileStart); ?></a> </h3>
    </div>
</div>
<div class="cms2cms-footer cms2cms-plugin">
    <a href="<?php echo $config['public_host']?>/how-it-works/"><?php $viewProvider->_e('How It Works', $fileStart); ?></a>
    <a href="<?php echo $config['public_host']?>/faqs/"><?php $viewProvider->_e('FAQs', $fileStart); ?></a>
    <a href="<?php echo $config['public_host']?>/blog/"><?php $viewProvider->_e('Blog', $fileStart); ?></a>
    <a href="<?php echo $config['public_host']?>/terms-of-service/"><?php $viewProvider->_e('Terms', $fileStart); ?></a>
    <a href="<?php echo $config['public_host']?>/privacy-policy/"><?php $viewProvider->_e('Privacy', $fileStart); ?></a>
    <a href="<?php echo $config['ticket']?>"><?php $viewProvider->_e('Submit a Ticket', $fileStart); ?></a>
</div>
<!-- start Mixpanel -->
<script type="text/javascript">
    (function(e,b){if(!b.__SV){var a,f,i,g;window.mixpanel=b;a=e.createElement("script");a.type="text/javascript";a.async=!0;a.src=("https:"===e.location.protocol?"https:":"http:")+'//cdn.mxpnl.com/libs/mixpanel-2.2.min.js';f=e.getElementsByTagName("script")[0];f.parentNode.insertBefore(a,f);b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==
    typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.track_charge people.clear_charges people.delete_user".split(" ");for(g=0;g<i.length;g++)f(c,i[g]);
        b._i.push([a,e,d])};b.__SV=1.2}})(document,window.mixpanel||[]);
    mixpanel.init("f48baf7f57bdb924fc68a786600d844e");
    mixpanel.identify("<?php echo md5($dataProvider->getUserEmail()); ?>");
</script>
<!-- end Mixpanel -->
<div id="cms_overlay">
    <div class="circle-an">
        <span class="dot no1"></span>
        <span class="dot no2"></span>
        <span class="dot no3"></span>
        <span class="dot no4"></span>
        <span class="dot no5"></span>
        <span class="dot no6"></span>
        <span class="dot no7"></span>
        <span class="dot no8"></span>
        <span class="dot no9"></span>
        <span class="dot no10"></span>
    </div>
</div>