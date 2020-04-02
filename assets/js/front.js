/*global $, document, Chart, LINECHART, data, options, window*/
$(document).ready(function () {

    'use strict';

    // ------------------------------------------------------- //
    // For demo purposes only
    // ------------------------------------------------------ //

    if ($.cookie("theme_csspath")) {
        $('link#theme-stylesheet').attr("href", $.cookie("theme_csspath"));
    }

    $("#colour").change(function () {

        if ($(this).val() !== '') {

            var theme_csspath = 'css/style.' + $(this).val() + '.css';

            $('link#theme-stylesheet').attr("href", theme_csspath);

            $.cookie("theme_csspath", theme_csspath, { expires: 365, path: document.URL.substr(0, document.URL.lastIndexOf('/')) });
        }

        return false;
    });

    // ------------------------------------------------------- //
    // Search Box
    // ------------------------------------------------------ //
    $('#search').on('click', function (e) {
        e.preventDefault();
        $('.search-box').fadeIn();
    });
    $('.dismiss').on('click', function () {
        $('.search-box').fadeOut();
    });

    // ------------------------------------------------------- //
    // Card Close
    // ------------------------------------------------------ //
    $('.card-close a.remove').on('click', function (e) {
        e.preventDefault();
        $(this).parents('.card').fadeOut();
    });


    // ------------------------------------------------------- //
    // Adding fade effect to dropdowns
    // ------------------------------------------------------ //
    $('.dropdown').on('show.bs.dropdown', function () {
        $(this).find('.dropdown-menu').first().stop(true, true).fadeIn();
    });
    $('.dropdown').on('hide.bs.dropdown', function () {
        $(this).find('.dropdown-menu').first().stop(true, true).fadeOut();
    });


    // ------------------------------------------------------- //
    // Login  form validation
    // ------------------------------------------------------ //
    $('#login-form').validate({
        messages: {
            loginEmail: 'please enter your valid email',
            loginPassword: 'please enter your password'
        }
    });

    // ------------------------------------------------------- //
    // Reset password  form validation
    // ------------------------------------------------------ //
    $('#resetpass-form').validate({
        messages: {
            resetPass: 'please enter your password',
            retypeResetPass: 'please enter your retype-password'
        }
    });

    

    // ------------------------------------------------------- //
    // Register form validation
    // ------------------------------------------------------ //
    $('#register-form').validate({
        messages: {
            registerFirstname: 'please enter your Firstname',
            registerMiddlename: 'please enter your Middlename',
            registerLastname: 'please enter your Lastname',
            registerBday: 'please enter your Birthdate',
            registerContactno: 'please enter your Contact no.',
            registerAddress: 'please enter your Address',
            registerCity: 'please enter your City',
            registerCountry: 'please enter your Country',
            registerEmail: 'please enter your valid Email',
            registerPassword: 'please enter your Password',
            registerRepassword: 'please enter your Re-type password'
            // registerUsername: 'please enter your first name',
            // registerEmail: 'please enter a vaild Email Address',
            // registerPassword: 'please enter your password'
        }
    });

    // ------------------------------------------------------- //
    // Sidebar Functionality
    // ------------------------------------------------------ //
    $('#toggle-btn').on('click', function (e) {
        e.preventDefault();
        $(this).toggleClass('active');

        $('.side-navbar').toggleClass('shrinked');
        $('.content-inner').toggleClass('active');

        if ($(window).outerWidth() > 1183) {
            if ($('#toggle-btn').hasClass('active')) {
                $('.navbar-header .brand-small').hide();
                $('.navbar-header .brand-big').show();
            } else {
                $('.navbar-header .brand-small').show();
                $('.navbar-header .brand-big').hide();
            }
        }

        if ($(window).outerWidth() < 1183) {
            $('.navbar-header .brand-small').show();
        }
    });

    // ------------------------------------------------------- //
    // Transition Placeholders
    // ------------------------------------------------------ //
    $('input.input-material').on('focus', function () {
        $(this).siblings('.label-material').addClass('active');
    });

    $('input.input-material').on('blur', function () {
        $(this).siblings('.label-material').removeClass('active');

        if ($(this).val() !== '') {
            $(this).siblings('.label-material').addClass('active');
        } else {
            $(this).siblings('.label-material').removeClass('active');
        }
    });

    // ------------------------------------------------------- //
    // External links to new window
    // ------------------------------------------------------ //
    $('.external').on('click', function (e) {

        e.preventDefault();
        window.open($(this).attr("href"));
    });

});
