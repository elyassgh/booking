/*  ---------------------------------------------------
  Template Name: Hiroto
  Description:  Hiroto Hotel HTML Template
  Author: Colorlib
  Author URI: https://colorlib.com
  Version: 1.0
  Created: Colorlib
---------------------------------------------------------  */

'use strict';

(function ($) {

    /*------------------
        Preloader
    --------------------*/
    $(window).on('load', function () {
        $(".loader").fadeOut();
        $("#preloder").delay(200).fadeOut("slow");
    });

    /*------------------
        Background Set
    --------------------*/
    $('.set-bg').each(function () {
        var bg = $(this).data('setbg');
        $(this).css('background-image', 'url(' + bg + ')');
    });

    //Canvas Menu
    $(".canvas__open").on('click', function () {
        $(".offcanvas-menu-wrapper").addClass("active");
        $(".offcanvas-menu-overlay").addClass("active");
    });

    $(".offcanvas-menu-overlay").on('click', function () {
        $(".offcanvas-menu-wrapper").removeClass("active");
        $(".offcanvas-menu-overlay").removeClass("active");
    });

    /*------------------
		Navigation
	--------------------*/
    $(".menu__class").slicknav({
        appendTo: '#mobile-menu-wrap',
        allowParentLinks: true
    });

    /*--------------------------
        Gallery Slider
    ----------------------------*/
    $(".gallery__slider").owlCarousel({
        loop: true,
        margin: 10,
        items: 4,
        dots: false,
        smartSpeed: 1200,
        autoHeight: false,
        autoplay: true,
        responsive: {
            992: {
                items: 4
            },
            768: {
                items: 3
            },
            576: {
                items: 2
            },
            0: {
                items: 1
            }
        }
    });

    /*--------------------------
        Room Pic Slider
    ----------------------------*/
    $(".room__pic__slider").owlCarousel({
        loop: true,
        margin: 0,
        items: 1,
        dots: false,
        nav: true,
        navText: ["<i class='arrow_carrot-left'></i>", "<i class='arrow_carrot-right'></i>"],
        smartSpeed: 1200,
        autoHeight: false,
        autoplay: false
    });

    /*--------------------------
        Room Details Pic Slider
    ----------------------------*/
    $(".room__details__pic__slider").owlCarousel({
        loop: true,
        margin: 10,
        items: 2,
        dots: false,
        nav: true,
        navText: ["<i class='arrow_carrot-left'></i>", "<i class='arrow_carrot-right'></i>"],
        autoHeight: false,
        autoplay: false,
        mouseDrag: false,
        responsive: {
            576: {
                items: 2
            },
            0: {
                items: 1
            }
        }
    });
    
    /*--------------------------
        Testimonial Slider
    ----------------------------*/
    var testimonialSlider = $(".testimonial__slider");
    testimonialSlider.owlCarousel({
        loop: true,
        margin: 30,
        items: 1,
        dots: true,
        nav: true,
        navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
        smartSpeed: 1200,
        autoHeight: false,
        autoplay: true,
        mouseDrag: false,
        onInitialized: function(e) {
        	    var a = this.items().length;
                $("#snh-1").html("<span>01</span><span>" + "0" + a + "</span>");
                var presentage = Math.round((100 / a));
                $('.slider__progress span').css("width", presentage + "%");
                
            }
        }).on("changed.owl.carousel", function(e) {
            var b = --e.item.index, a = e.item.count;
            $("#snh-1").html("<span> "+ "0" +(1 > b ? b + a : b > a ? b - a : b) + "</span><span>" + "0" + a + "</span>");

            var current = e.page.index + 1;
            var presentage = Math.round((100 / e.page.count) * current);
            $('.slider__progress span').css("width", presentage + "%");
    });
    
    
    /*--------------------------
        Logo Slider
    ----------------------------*/
    $(".logo__carousel").owlCarousel({
        loop: true,
        margin: 100,
        items: 5,
        dots: false,
        smartSpeed: 800,
        autoHeight: false,
        autoplay: true,
        responsive: {
            992: {
                items: 5
            },
            768: {
                items: 3
            },
            320: {
                items: 2
            },
            0: {
                items: 1
            }
        }
    });

    /*--------------------------
        Select
    ----------------------------*/
    $("select").niceSelect();
    

    /*--------------------------
        Datepicker
    ----------------------------*/
    var mS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    function formater (myDate) { 
        var dd = myDate.getDate(); 
        var mm = myDate.getMonth() + 1; 
        var yyyy = myDate.getFullYear(); 
        var month; 
        if (dd < 10) { 
            dd = '0' + dd; 
        } 
        for (let i = 0; i <= 12; i++) {
            const element = mS[i];
            if (mm == mS.indexOf(mS[i])) {
                month = mS[i-1];
            }
        }
        return dd + ' ' + month + ' ' + yyyy; 
    }

    function parse (myString) { 
        return new Date(myString);
    }

    function addDays(days) { 
        var myDay = new Date();
        myDay.setDate(myDay.getDate() + days)
        return myDay;
    }

   // $(".check__in").val(formater(addDays(0)));
   // $(".check__out").val(formater(addDays(1)));

    $(".datepicker_pop_in").datepicker({ 
        dateFormat: 'dd M yy',
        minDate: addDays(0),
        maxDate: addDays(29)
    });

    
    $(".datepicker_pop_out").datepicker({ 
        dateFormat: 'dd M yy',
        minDate: addDays(1),
        maxDate: addDays(30)
    });

    
    $(".check__in").change(function () { 
        var minCheckout = parse($(".check__in").val());
        minCheckout.setDate(minCheckout.getDate() + 1);
        $(".datepicker_pop_out").datepicker( "option", "minDate", minCheckout );
        $(".datepicker_pop_out").datepicker( "refresh" );
    });

    $(".check__out").change(function () { 
        var maxCheckout = parse($(".check__out").val());
        maxCheckout.setDate(maxCheckout.getDate() - 1);
        $(".datepicker_pop_in").datepicker( "option", "maxDate", maxCheckout );
        $(".datepicker_pop_in").datepicker( "refresh" );
    });

    /*--------------------------
        Testimonial Slider
    ----------------------------*/
    $(document).ready(function(){
        var rangeSlider = function(){
            var slider = $('.range-slider'),
                range = $('.range-slider input[type="range"]'),
                value = $('.range-value');
            slider.each(function(){
                value.each(function(){
                    var value = $(this).prev().attr('value');
                    var max = $(this).prev().attr('max');
                    $(this).html(value);
                });
                range.on('input', function(){
                    $(this).next(value).html(this.value);
                });
            });
        };
        rangeSlider();
    });

})(jQuery);