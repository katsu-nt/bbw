//scroll header
// $(function () {
//   $(document).ready(function(){
//     var viewportGlobal = $(window).width();
//     var nav = $('.l-nav');
//     var body = $('body');
//     var headerBannerHeight = 0;
//     var headerTotal = 0;
//     if($('.c-header-banner').length > 0){
//       var headerBanner = $('.c-header-banner');
//       headerBannerHeight = headerBanner.height();
//       headerTotal = headerBannerHeight;
//     }
//     if(viewportGlobal > 991){
//       headerTotal = headerBannerHeight + 30;
//       if ($('.l-nav.is-settings').length > 0){
//         headerTotal = headerBannerHeight;
//       }
//     }
//     if(nav.length > 0){
//       $(window).scroll(function(){
//         if($(this).scrollTop() > headerTotal){
//           nav.addClass('has-fixed');
//           body.addClass('has-padding');
//         }
//         if($(this).scrollTop() <= headerTotal){
//           nav.removeClass('has-fixed');
//           body.removeClass('has-padding');
//         }
//       });
//     }
//     // Hide Header on on scroll down
//     var didScroll;
//     var lastScrollTop = 0;
//     var delta = 5;
//     var navbarHeight = nav.outerHeight();
//
//     $(window).scroll(function(event){
//       didScroll = true;
//     });
//
//     setInterval(function() {
//       if (didScroll) {
//         hasScrolled();
//         didScroll = false;
//       }
//     }, 250);
//
//     function hasScrolled() {
//       var st = $(this).scrollTop();
//
//       // Make sure they scroll more than delta
//       if(Math.abs(lastScrollTop - st) <= delta)
//         return;
//
//       // If they scrolled down and are past the navbar, add class .nav-up.
//       // This is necessary so you never see what is "behind" the navbar.
//       if (st > lastScrollTop && st > navbarHeight){
//         // Scroll Down
//         nav.removeClass('nav-down').addClass('nav-up');
//       } else {
//         // Scroll Up
//         if(st + $(window).height() < $(document).height()) {
//           nav.removeClass('nav-up').addClass('nav-down');
//         }
//       }
//
//       lastScrollTop = st;
//     }
//   });
// });

//var function menu click
$(function () {
  $(document).ready(function(){
    if($('.js-menu-expand').length > 0){
      $('.js-menu-expand').click(function(e) {
        e.preventDefault();
        var body = $('body');
        var menu = $('.c-menu-wrapper');
        if (menu.hasClass('has-menu-tiny')) {
          $(this).removeClass('active');
          body.removeClass('has-body-menu');
          menu.removeClass('has-menu-tiny')
              .slideUp();
        } else {
          $(this).addClass('active');
          body.addClass('has-body-menu');
          menu.addClass('has-menu-tiny')
              .slideDown();
        }
      });
    }
  });
});

//var function search click
$(function () {
  $(document).ready(function(){
    if($('.js-expand-search').length > 0){
      $('.js-expand-search').click(function(e) {
        e.preventDefault();
        var search = $('.c-search-wrapper');
        if (search.hasClass('active')) {
          $(this).removeClass('active');
          search.removeClass('active');
        } else {
          $(this).addClass('active');
          search.addClass('active');
        }
      });
    }
  });
});

//var function menu click
$(function () {
  $(document).ready(function(){
    if($('.js-menu-expand-pc').length > 0){
      $('.js-menu-expand-pc').click(function(e) {
        e.preventDefault();
        var body = $('body');
        if (body.hasClass('has-menu')) {
          $(this).removeClass('active');
          body.removeClass('has-menu');
        } else {
          $(this).addClass('active');
          body.addClass('has-menu');
        }
      });
    }
  });
});

//var function menu close
$(function () {
  $(document).ready(function(){
    if($('.js-menu-close').length > 0){
      $('.js-menu-close').click(function(e) {
        e.preventDefault();
        var body = $('body');
        var menuBtn = $('.js-menu-expand');
        menuBtn.removeClass('active');
        body.removeClass('has-menu');
      });
    }
  });
});

//var function one slider
$(function () {
  $(document).ready(function(){
    if($('.js-one-slider').length > 0){
      $('.js-one-slider').owlCarousel({
        loop:true,
        items:1,
        margin:0,
        responsiveClass:false,
        nav:false,
        dots:true,
        autoplay:true,
        autoHeight:false,
        autoplayTimeout:6000,
        autoplaySpeed:1000,
        autoplayHoverPause:true,
        navText:false
      });
    }
  });
});