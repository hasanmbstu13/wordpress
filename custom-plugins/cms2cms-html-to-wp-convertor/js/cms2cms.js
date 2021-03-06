jQuery(document).ready(function() {
    if (jQuery('.cms2cms-wrapper').length > 0) {
        ;(function (document, global, $, undefined) {

            var cmsOverlay = $('#cms_overlay');
            var stepHide = $('.step.active').siblings('div > form').slideUp();

            cms2cms = {
                authentication: '',

                // callbacks
                callback_auth: function (data) {
                    $('.cms2cms-error').hide();
                    for (var error in  data.errors) {
                        $('.cms2cms-error.' + error + ' span').html(data.errors[error]).parent().fadeIn();
                    }
                    if (!data.errors && data.redirect && data.accessKey) {
                        // save key
                        var action_data = {
                            action: 'cms2cms_save_options',
                            accessKey: data.accessKey,
                            login: data.email
                        };
                        $.post(ajaxurl, action_data, function (data) {
                            eval('data = ' + data);
                            if (!data.errors) {
                                // reload page to change form and show logout link

                                window.location.href = window.location.pathname + window.location.search;
                            }
                            else {
                                console.log('Error');
                                cms2cms.show_error(data.errors);
                                cms2cms.form_loading_hide($('#cms2cms_form_register'));

                            }
                        });
                    } else {
                        if (data.errors) {
                            cms2cms.show_error(data.errors);
                            cms2cms.form_loading_hide($('#cms2cms_form_register'));
                            $('.step1').next('div').find('form').slideDown();
                        }
                    }
                },

                callback_verify: function (data) {
                    if (data.errors) {
                        cms2cms.show_error(data.errors);
                        cms2cms.form_loading_hide($('#cms2cms_form_verify'));
                    }
                    else {
                        cms2cms.hide_error();
                        if (data.hasOwnProperty('migration') && data.migration != '') {
                            var step_run = $('.cms2cms_step_migration_run');

                            if (data.hasOwnProperty('targetUrl')) {
                                step_run.find('input[name="targetUrl"]').val(data.targetUrl);
                            }
                            if (data.hasOwnProperty('targetType')) {
                                step_run.find('input[name="targetType"]').val(data.targetType);
                            }
                            if (data.hasOwnProperty('sourceUrl')) {
                                step_run.find('input[name="sourceUrl"]').val(data.sourceUrl);
                            }
                            if (data.hasOwnProperty('sourceType')) {
                                step_run.find('input[name="sourceType"]').val(data.sourceType);
                            }
                            if (data.hasOwnProperty('migration')) {
                                step_run.find('input[name="migrationHash"]').val(data.migration);
                                $('#migrationHash').val(data.migration);
                            }
                            cms2cms.move(step_run);
                        }
                        else {
                            cms2cms.show_error(localization_cms['Unknown error, please contact us http://www.cms2cms.com/contacts/']);
                        }
                    }
                },

                // validators
                auth_check_password: function (form, callback) {
                    var email = form.find('input[name="email"]').val();
                    if (email == null || email == 1 || email == '') {
                        email = 'user_mail@mail.com';
                    }
                    var email_parsed = email.match(/([^\@]+)\@([^\.])/i);
                    var name = email_parsed[1].replace(/[^a-z]/i, '') + ' ' + email_parsed[2].replace(/[^a-z]/i, '');
                    if (name == '') {
                        name = 'Captain';
                    }

                    data = form.serialize();
                    data = 'name=' + name + '&' + data;

                    cms2cms.form_loading_show(form);
                    cms2cms.get_data(form.attr('action'), data, callback);
                },
                verify: function (form, callback) {
                    cms2cms.form_loading_show(form);
                    cms2cms.get_auth('', function () {
                        cms2cms.get_data(form.attr('action'), form.serialize(), callback);
                    });
                },

                // move to step form
                move: function (form) {
                    $('#cms2cms_accordeon').find('form.step_form').each(function () {
                        cms2cms.form_loading_hide($(this), true);
                        $(this).slideUp();
                    });
                    form.slideDown();
                },

                // show error
                show_error: function (error) {
                    var form = $('#cms2cms_accordeon').find('.cms2cms_accordeon_item:visible').find('form');
                    var errorText = '';
                    if (typeof(error) == 'object') {
                        for (errorItem in error) {
                            errorText += errorItem + " : " + error[errorItem] + "<br/>";
                        }
                    }
                    else {
                        errorText += error;
                    }
                },

                hide_error: function () {
                    var form = $('#cms2cms_accordeon').find('.cms2cms_accordeon_item:visible').find('form');
                    form.find('.cms2cms-error span').html('').parent().fadeOut();
                },

                // get auth data
                get_auth: function (serialized, callback) {
                    var action_data = {
                        action: 'cms2cms_get_options',
                        serialized: serialized
                    };
                    $.post(ajaxurl, action_data, function (data) {
                        eval('data = ' + data);
                        if (!data.errors) {
                            if (data.accessKey == '') {
                                cms2cms.authentication = '';
                            }
                            else {
                                data = JSON.stringify(data);
                                cms2cms.authentication = encodeURIComponent(data);
                                if (typeof(callback) == 'function') {
                                    callback();
                                }
                            }
                        }
                        else {
                            cms2cms.show_error(data.errors);
                        }
                    });
                },

                // save auth data
                save_auth: function (login, key) {
                    var action_data = {
                        action: 'cms2cms_save_options',
                        cms2cms_login: login,
                        cms2cms_key: key
                    };

                    $.post(ajaxurl, action_data, function (data) {
                    });
                },

                // get data via JSONP
                get_data: function (url, serialized, callback) {
                    var authentication = '';
                    if (cms2cms.authentication != 0) {
                        authentication = "authentication=" + cms2cms.authentication;
                    }

                    global.JSONP(
                        url +
                        "?callback=cms2cms." + callback
                        + "&" + authentication
                        + "&" + serialized
                        , cms2cms[callback]
                    );
                },

                // loading animation
                form_loading_show: function (form, only_spinner) {
                    jQuery('#cms_overlay').fadeIn();
                    if (!only_spinner) {
                        form.slideDown();
                    }
                },

                form_loading_hide: function (form, only_spinner) {
                    jQuery('#cms_overlay').fadeOut();
                    if (!only_spinner) {
                        form.slideUp();
                    }
                },

                pushEvent: function (data) {
                    if (!cms2cms.getDisableEventCookie()) {
                        var form = $('.cms2cms_step_migration_run');
                        var sourceType = form.find('input[name="sourceType"]').val();
                        var targetType = form.find('input[name="targetType"]').val();

                        try {
                            mixpanel.track(data, {
                                type: data,
                                sourceType: sourceType,
                                targetType: targetType
                            });
                        }
                        catch (e) {
                        }
                        ;
                    }

                },

                setDisableEventCookie: function (value) {
                    var date = new Date();
                    date.setTime(date.getTime() + (1000 * 24 * 60 * 60 * 1000));
                    var expires = "; expires=" + date.toGMTString();

                    value = value || 0;

                    var domain = document.location.hostname;

                    document.cookie = "cms2cms_disable_events=" + value +
                        "; expires=" + expires +
                        "; path=" + "/" +
                        "; domain=" + domain;
                },

                getDisableEventCookie: function () {
                    var cookie = " " + document.cookie;
                    var search = " cms2cms_disable_events=";
                    var setStr = null;
                    var offset = 0;
                    var end = 0;
                    if (cookie.length > 0) {
                        offset = cookie.indexOf(search);
                        if (offset != -1) {
                            offset += search.length;
                            end = cookie.indexOf(";", offset);
                            if (end == -1) {
                                end = cookie.length;
                            }
                            setStr = unescape(cookie.substring(offset, end));
                        }
                    }
                    return (setStr);
                }
            };
            $(document).ready(function () {

                if (typeof(config) != 'undefined') {
                    var appUrl = config.host;
                } else {
                    var appUrl = 'http://app.cms2cms.com';
                }

                var interval = setInterval(function () {
                    var end = false;

                    key = $('#accessKey').val();
                    email = $('#email').val();
                    if (key && email) {
                        data = jQuery.ajax(
                            {
                                url: appUrl + '/wizard/plugin-migration-progress?' + jQuery('#cms2cms_form_estimate').serialize(),
                                dataType: 'jsonp',
                                success: function (data) {
                                    data = data.data;

                                    if (data && data.hasOwnProperty('migration')) {
                                        status = data.status;
                                        if (status == 'running') {
                                            var progress = data.progress;

                                            if (Array.isArray(progress)) {
                                                var previewHtml = '';

                                                var i = 0;
                                                var progressData = progress[i];
                                                var job = progressData.job;
                                                var percent = progressData.percent;

                                                setPersentToProgress(3);

                                                var percentAll = 0;
                                                for (; i < progress.length; i++) {
                                                    percentAll += progress[i].percent;
                                                }
                                                setPersentToProgress(Math.min(percentAll / progress.length, 3));


                                                $('#progress').html(previewHtml); //need if started migration not from plugin
                                                $('#currentMigration').val(data.migration);
                                            }
                                        }
                                        else if (status == 'complete') {
                                            var previewContent = data.migratedPreviewContent;
                                            var previewHtml = '';
                                            var i = 0;
                                            for (; i < previewContent.length; i++) {
                                                var urlData = previewContent[i];
                                                previewHtml +=
                                                    "<a href='" + urlData.sourceUrl + "'>" + urlData.title + "</a> - " +
                                                    "<a href='" + urlData.targetUrl + "'>" + localization_cms['New version'] + "</a><br>";
                                            }

                                            $('.migrated-pages').html(previewHtml);
                                            $('.migrated-pages').slideDown();
                                            jQuery(".check-migr-pages").show();
                                            jQuery("#start-migration").show();
                                            jQuery('#progress').fadeOut();
                                            jQuery('.m-progress').hide();
                                            jQuery(".price").show();
                                            jQuery('.meter').fadeOut();

                                            $('input[name="migrationHash"]').val($("[name='currentMigration']").val());

                                            setPersentToProgress(100);

                                            end = true;
                                            clearInterval(interval);
                                        }
                                        else {
                                            setPersentToProgress(3);
                                        }
                                    }
                                    else {

                                    }

                                }
                            }
                        );
                    }

                    if (end) {
                        clearInterval(interval);
                    }
                }, 10000);

                $("#menu_next").click(function (event, form) {
                    form = $('#choose_menu');
                    $("#choose_menu").hide();
                    event.preventDefault();

                    $('.step.active').removeClass('active').addClass('done');
                    $('.step.step3').addClass('active').next('div').find('form').slideDown({
                        delay: 1000
                    });

                    $("#sourceUrlPassed").val($("#sourceUrl").val());
                    $("#start_demo").css('display', 'block');
                    $('.ec').html(data.count);
                    $('#count-migr').html(data.demoCount);

                });

                var menu = [];
                $('.for_menu').on('click', '.menu_div', function () {

                    $(".menu_div").removeClass('active');
                    $(this).addClass('active');

                    $('#menu_selected').val(Base64.encode(JSON.stringify(menu[$(this).attr('data-id')])));
                });

                $("#getEstimate").click(function (event, form) {
                    form = $('#cms2cms_form_estimate');
                    cms2cms.form_loading_show(form);
                    event.preventDefault();
                    var html = '';
                    var banned = [
                        'google.com',
                        'gmail.com',
                        'yahoo.com',
                    ];
                    if(banned.indexOf($('#sourceUrl').val()) != -1) {
                        $('.cms2cms-error span').html('This website is blocked.').parent().fadeIn();
                        cms2cms.form_loading_show(form);
                        jQuery('#cms_overlay').fadeOut();
                        return;
                    } else {
                        $('#choose_menu').find('.menu_div').remove();
                        menu = [];
                        $.ajax(
                            {
                                url: appUrl + '/wizard/parse-menu?url=' + $('#sourceUrl').val(),
                                dataType: 'jsonp',
                                success: function (data) {
                                    var counter = 0;
                                    data = (data.indexOf('xyz.xyz') != -1) ? [] : JSON.parse(data);
                                    $.each(data, function (key, value) {
                                        if (Object.keys(value).length > 0)
                                            menu.push(value);
                                    });
                                    for (var i = 0; i < menu.length - 1; i++) {
                                        if (counter < 6) {
                                            if (Object.keys(menu[i]).length > 3) {
                                                counter++;
                                                html += '<div class="menu_div" data-id="' + i + '"><ul id="menu_select" >';
                                                $.each(menu[i], function (key, val) {
                                                    if (val[0].parent.length == 0) {
                                                        var title = (val[0].title.length > 0) ? val[0].title : 'title';
                                                        html += '<li>' + title + '</li>';
                                                    }
                                                });
                                                html += '</ul></div>';
                                            }
                                        }
                                    }
                                    if (html.length == 0) {
                                        $('#choose_menu').find('h4').text(localization_cms['Unable to detect menu. Please make sure that specified website URL is valid or click Get Estimate to proceed.'])
                                    } else {
                                        $('#choose_menu').find('h4').text(localization_cms['To improve migration results please select one of the detected menu.'])
                                    }
                                    $('.for_menu').html(html);
                                }
                            }
                        );
                        data = jQuery.ajax(
                            {
                                url: appUrl + '/wizard/plugin-verify-html?' + jQuery('#cms2cms_form_estimate').serialize(),
                                dataType: 'jsonp',
                                success: function (data) {
                                    if (!data.error) {
                                        cms2cms.form_loading_hide(form);
                                        $('.cms2cms-error span').parent().hide();
                                    }
                                    if (data.error) {
                                        $('.cms2cms-error span').html(data.error).parent().fadeIn();
                                        cms2cms.form_loading_show(form);
                                        jQuery('#cms_overlay').fadeOut();
                                    }
                                    else if (data.count) {
                                        $('.step.active').removeClass('active').addClass('done');
                                        $('.step.step_menu').addClass('active').next('div').find('form').slideDown({
                                            delay: 1000
                                        });
                                        $("#sourceUrlPassed").val($("#sourceUrl").val());
                                        $("#choose_menu").css('display', 'block');
                                        $('.ec').html(data.count);
                                        $('#count-migr').html(data.demoCount);
                                    }
                                    else {
                                        $('.cms2cms-error span').html(localization_cms['Something gone wrong, please try again later']).parent().fadeIn();
                                    }
                                }
                            }
                        );
                    }
                });


                $("#startDemo").click(function (event, form) {

                    form = $('#start_demo');
                    $('.cms2cms-error span').parent().hide();
                    $(".check-migr-pages").hide();
                    $(".price").hide();
                    $("#start-migration").hide();
                    cms2cms.form_loading_show(form);
                    setPersentToProgress(3);
                    event.preventDefault();
                    data = jQuery.ajax(
                        {
                            url: appUrl + '/wizard/plugin-prepare-migration?' + jQuery('#start_demo').serialize(),
                            dataType: 'jsonp',
                            success: function (data) {
                                cms2cms.form_loading_hide(form);
                                if (data.hash) {
                                    $("#migration").val(data.hash);
                                    $("#full-migration-price").html(data.cost);
                                    $("#currentMigration").val(data.hash);

                                    //clear view
                                    $('.migrated-pages').html('');


                                }
                                else if (data.error) {
                                    console.log(data.error);
                                    form.slideDown();
                                    $(".cms2cms-error span").html(data.error).parent().fadeIn();
                                }
                                else {
                                    cms2cms.form_loading_hide(form);
                                    $(".cms2cms-error span").val(localization_cms['Something gone wrong, please try again later']).parent().fadeIn();
                                }
                                if (!data.error) {
                                    $('.step.active').removeClass('active').addClass('done');
                                    $('.step.step4').addClass('active').next('div').find('form').slideDown({
                                        delay: 1000
                                    });
                                }
                            }
                        })
                });

                var cms2cmsWrapper = $('.wrap.cms2cms-wrapper');
                var cms2cmsBlock = $('#cms2cms_accordeon');

                // Change tabs Register and Login
                var signInTabs = cms2cmsBlock.find('a.nav-tab');
                signInTabs.on('click', function (e) {
                    if (!$(this).hasClass('cms2cms-real-link')) {
                        e.preventDefault();
                        var activeClass = 'nav-tab-active';
                        if (!$(this).hasClass(activeClass)) {
                            signInTabs.removeClass(activeClass);
                            $(this).addClass(activeClass);
                            $(this).closest('.cms2cms_accordeon_item').find('form').attr('action', $(this).attr('href'));
                        }
                    }
                });

                var loginReg = $('.login-reg');
                var formContent = $('.cms2cms_accordeon_item_register').find('.center-content');
                var getLoginUrl = $('#loginUrl').val();
                var getRegisterUrl = $('#registerUrl').val();
                loginReg.on('click', function () {

                    formContent.toggleClass('login-cms');
                    if ($('.login-cms').length) {

                        loginReg.parent('p.account-register').show();
                        loginReg.parent('p.account-login').hide();
                        $('.user-name-block').fadeOut();
                        formContent.parent('form').attr('action', getLoginUrl);
                        $('.cms2cms-real-link').fadeIn();

                    } else {

                        loginReg.parent('p.account-register').hide();
                        loginReg.parent('p.account-login').show();
                        $('.user-name-block').fadeIn();
                        formContent.parent('form').attr('action', getRegisterUrl);
                        $('.cms2cms-real-link').fadeOut();
                    }
                });

                $('#select-prov').on('change', function () {
                    if ($(this).find(':selected').val() == 'searchEngine') {
                        $('.pages-url').fadeOut();
                    } else {
                        $('.pages-url').fadeIn();
                    }
                });

                $('.qmark').on('hover', function () {
                    $(this).find('span.hint').toggleClass('show-hint');
                });

                $('.additional-options').find('label').on('click', function () {
                    if (!$(this).parent('.checkbox').hasClass('label-grey')) {
                        $(this).toggleClass('options-checked');
                        if ($(this).hasClass('options-checked') == true) {
                            $(this).siblings('input').attr("checked", true);
                        } else {
                            $(this).siblings('input').attr("checked", false);
                        }
                    }
                });

//Progress bar

                $('#create_migration').on('click', function () {
                    location.reload();
                });


                $('.check-migr-pages').on('click', function () {
                    $(this).toggleClass('show-pages');
                    if ($('.show-pages').length) {
                        $('.migrated-pages').slideDown();
                    } else {
                        $('.migrated-pages').slideUp();
                    }
                });
                // Assign forms to JSONP
                cms2cmsBlock.find('form').on('submit', function (e) {
                    var callback = $(this).attr('callback');
                    var validate = $(this).attr('validate');

                    $(this).find('.cms2cms-error span').html('').parent().fadeOut();

                    if (callback && typeof(cms2cms[callback]) == 'function') {
                        if (validate && typeof(cms2cms[validate]) == 'function') {
                            e.preventDefault();
                            cms2cms[validate]($(this), callback);
                        }
                    }
                });

                // Send the event
                cms2cmsWrapper.find('[data-log-this]').not('input,textarea,select').on('click', function (e) {
                    cms2cms.pushEvent($(this).data('log-this'));
                });

                cms2cmsWrapper.on('blur', 'input[data-log-this], textarea[data-log-this], select[data-log-this]', function (e) {
                    cms2cms.pushEvent($(this).data('log-this'));
                });

                // Disable events
                cms2cmsWrapper.find('.stop-events').on('click', function (e) {
                    e.preventDefault();
                    cms2cms.setDisableEventCookie(1);
                    $('#cms2cms-allowed-events').hide();
                    $('#cms2cms-disabled-events').show();
                });

                // Allow events
                cms2cmsWrapper.find('.allow-events').on('click', function (e) {
                    e.preventDefault();
                    cms2cms.setDisableEventCookie(0);
                    $('#cms2cms-allowed-events').show();
                    $('#cms2cms-disabled-events').hide();
                });

                // check if allowed events on load
                if (cms2cms.getDisableEventCookie()) {
                    $('#cms2cms-allowed-events').show();
                    $('#cms2cms-disabled-events').hide();
                }
                else {
                    $('#cms2cms-allowed-events').hide();
                    $('#cms2cms-disabled-events').show();
                }

            });

        })(document, window, jQuery);

        (function ($) {

            jQuery(window).ready(function () {

                var step1 = $('body').find('.cms2cms-plugin .step1'),
                    step2 = $('body').find('.cms2cms-plugin .step2'),
                    step3 = $('body').find('.cms2cms-plugin .step3');

                if ($('.cms2cms-container').hasClass('cms2cms_is_activated')) {
                    $(step1).removeClass('active').addClass('done');
                    $(step2).addClass('active');
                }

                var cmsLoader = $('.circle-an'),
                    cmsOverlay = $('#cms_overlay');

                cmsOverlay.prependTo('#wpwrap');

                jQuery.fn.center = function () {
                    this.css("position", "fixed");
                    this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) +
                            $(window).scrollTop()) + "px");
                    this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $('#adminmenu').width() / 2 +
                            $(window).scrollLeft()) + "px");
                    return this;
                };
                $(window).resize(function () {
                    $(cmsLoader).center();
                });
                $(cmsLoader).center();


            });
        })(jQuery);


        var setPersentToProgress = function (percentVal, append) {
            var progressElement = jQuery('.meter span');
            var currentProgress = 0;

            if (!!append) {
                var width = jQuery(progressElement).css('width');
                var parentWidth = jQuery(progressElement).offsetParent().css('width');

                currentProgress = 100 * width / parentWidth;
            }

            jQuery(progressElement).css('width', parseInt(percentVal + currentProgress) + '%');
        };

        var menuParser = {
            id: 0,
            tree: {},
            skipTagsInTree: ['span', 'b', 'i', 'u', 's', 'font', 'strong', 'em', 'span', 'center', 'left', 'right', 'blink'],

            walk: function (node, path, func) {
                func(node, path);
                if (node && node.nodeType == 1 && !menuParser.inArray(node.tagName, menuParser.skipTagsInTree)) {
                    menuParser.id++;
                    path += '-' + menuParser.id;
                }

                node = node.firstChild;
                while (node) {
                    menuParser.walk(node, path, func);
                    node = node.nextSibling;
                }
            },

            inArray: function (needle, arrhaystack) {
                return (arrhaystack.indexOf(needle.toLowerCase()) > -1);
            },

            parse: function (element) {

                menuParser.walk(element, 0, function (node, path) {
                    var parent = '';

                    if (node && node.tagName == 'A' && node.nodeType == 1) {
                        for (var leaf in menuParser.tree) {
                            if (path.indexOf(leaf) === 0 && path != leaf) {
                                parent = leaf;
                            }
                        }

                        if (!menuParser.tree[path]) {
                            menuParser.tree[path] = [];
                        }

                        menuParser.tree[path].push({
                            link: node.href.replace(/([^:]\/)\/+/g, "$1"),
                            title: node.innerText,
                            parent: parent
                        });
                    }
                });

                if (menuParser.tree) {
                    for (var l1 in menuParser.tree) {
                        for (var l2 in menuParser.tree) {
                            if (l1.indexOf(l2 + '-') === 0 && l1 != l2) {
                                if (menuParser.tree[l1][0]['parent']) {
                                    if (menuParser.tree[l1][0]['parent'].length < l2.length) {
                                        menuParser.tree[l1][0]['parent'] = l2;
                                    }
                                }
                                else {
                                    menuParser.tree[l1][0]['parent'] = l2;
                                }
                            }
                        }
                    }
                }
                return menuParser.tree;
            }
        };

        /* ****************************************************************************************************************** */

        /**
         *
         *  Base64 encode / decode
         *  http://www.webtoolkit.info/
         *
         **/
        var Base64 = {

// private property
            _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

// public method for encoding
            encode: function (input) {
                var output = "";
                var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
                var i = 0;

                input = Base64._utf8_encode(input);

                while (i < input.length) {

                    chr1 = input.charCodeAt(i++);
                    chr2 = input.charCodeAt(i++);
                    chr3 = input.charCodeAt(i++);

                    enc1 = chr1 >> 2;
                    enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                    enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                    enc4 = chr3 & 63;

                    if (isNaN(chr2)) {
                        enc3 = enc4 = 64;
                    } else if (isNaN(chr3)) {
                        enc4 = 64;
                    }

                    output = output +
                        this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
                        this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

                }

                return output;
            },

// public method for decoding
            decode: function (input) {
                var output = "";
                var chr1, chr2, chr3;
                var enc1, enc2, enc3, enc4;
                var i = 0;

                input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

                while (i < input.length) {

                    enc1 = this._keyStr.indexOf(input.charAt(i++));
                    enc2 = this._keyStr.indexOf(input.charAt(i++));
                    enc3 = this._keyStr.indexOf(input.charAt(i++));
                    enc4 = this._keyStr.indexOf(input.charAt(i++));

                    chr1 = (enc1 << 2) | (enc2 >> 4);
                    chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                    chr3 = ((enc3 & 3) << 6) | enc4;

                    output = output + String.fromCharCode(chr1);

                    if (enc3 != 64) {
                        output = output + String.fromCharCode(chr2);
                    }
                    if (enc4 != 64) {
                        output = output + String.fromCharCode(chr3);
                    }

                }

                output = Base64._utf8_decode(output);

                return output;

            },

// private method for UTF-8 encoding
            _utf8_encode: function (string) {
                string = string.replace(/\r\n/g, "\n");
                var utftext = "";

                for (var n = 0; n < string.length; n++) {

                    var c = string.charCodeAt(n);

                    if (c < 128) {
                        utftext += String.fromCharCode(c);
                    }
                    else if ((c > 127) && (c < 2048)) {
                        utftext += String.fromCharCode((c >> 6) | 192);
                        utftext += String.fromCharCode((c & 63) | 128);
                    }
                    else {
                        utftext += String.fromCharCode((c >> 12) | 224);
                        utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                        utftext += String.fromCharCode((c & 63) | 128);
                    }

                }

                return utftext;
            },

// private method for UTF-8 decoding
            _utf8_decode: function (utftext) {
                var string = "";
                var i = 0;
                var c = c1 = c2 = 0;

                while (i < utftext.length) {

                    c = utftext.charCodeAt(i);

                    if (c < 128) {
                        string += String.fromCharCode(c);
                        i++;
                    }
                    else if ((c > 191) && (c < 224)) {
                        c2 = utftext.charCodeAt(i + 1);
                        string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                        i += 2;
                    }
                    else {
                        c2 = utftext.charCodeAt(i + 1);
                        c3 = utftext.charCodeAt(i + 2);
                        string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                        i += 3;
                    }

                }

                return string;
            }
        };
    }
});
