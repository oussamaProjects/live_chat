"use strict";

/**
*
* L'nk Live Promo Module
*
* @author    Abdelakbir el Ghazouani - By L'nkboot.fr
* @copyright 2016-2020 Abdelakbir el Ghazouani - By L'nkboot.fr (http://www.lnkboot.fr)
* @license   Commercial license see license.txt
* @category  Module
* Support by mail  : contact@lnkboot.fr
*
*/
var fbReactions = ["like", "love"]; // var fbReactions = ["like", "love", "happy", "wow", "sad", "angry"]

var interval;
$(document).ready(function () {
  // Particles 
  $("body").on('click', '.lp_interactive_btn', function (e) {
    e.preventDefault(); // interval = setInterval(function () {

    $(".particles_container .particles").append("<span class='particle " + fbReactions[Number($(this).hasClass('love'))] + "'></span>");
    $(".particle").toArray().forEach(function (particale) {
      $(particale).animate({
        // left: getRndIntiger(0, $(".particles").width()),
        right: getRndIntiger(0, $(".particles").width() - 40)
      }, getRndIntiger(10, 30), function () {
        $(particale).animate({
          top: -100 + "%",
          opacity: 0
        }, getRndIntiger(2000, 6000), function () {
          $(particale).remove();
        });
      });
    }, getRndIntiger(1000, 2000));
  });
  $(window).on("blur", function () {
    clearInterval(interval);
  }); // ------------------ LIVE CHAT ELEMENTS

  chat_update_scrollbar(24);
  setTimeout(function () {
    $('.lp_side').addClass('closed');
  }, 500);
  $('.lp_head').click(function (e) {
    e.preventDefault();
    $('.lp_side').toggleClass('closed');
  });
  var matchMedia700 = window.matchMedia("(max-width: 700px)");
  $('.lp_side > .close').click(function (e) {
    e.preventDefault();
    $('.lp_side').toggleClass('closed');
    $('.lp_side').toggleClass('show_live');
    bodyOverflowToggle(matchMedia700);
  });
  $('.bubble').click(function (e) {
    e.preventDefault();
    $(this).toggleClass('shake');
    $('.lp_side').toggleClass('closed');
    $('.lp_side').toggleClass('show_live');
    $('.lp_side_chat .close').toggleClass('closed');
    $('.lp_side_chat_container').toggleClass('closed');
    bodyOverflowToggle(matchMedia700);
  }); // ------------------ SIDE LIVE CHAT ELEMENTS

  $('.lp_side_chat').addClass(LP_STYLE.lp_style_height_side_chat ? 'height-' + LP_STYLE.lp_style_height_side_chat : 'height-50');
  $('.lp-label.users').addClass(LP_STYLE.lp_style_live_label_position == 1 ? 'left' : 'right');
  $('.lp_feature_product_title').addClass(LP_STYLE.lp_style_feature_product_position == 1 ? 'left' : 'right');
  $('.lp_feature_product').addClass(LP_STYLE.lp_style_feature_product_display == 1 ? 'shown' : 'hidden');
  $('.lp_commentes_container').addClass(LP_STYLE.lp_style_feature_product_display == 1 ? 'has_feature_product' : '');

  if (LP_STYLE.lp_style_like_button == 1) {}

  $(".lp_footer .lp_icons").addClass(LP_STYLE.lp_style_like_button == 1 || LP_STYLE.lp_style_love_button == 1 || LP_STYLE.lp_style_dark_light_mode_button == 1 ? 'has_element' : '');

  if (LP_STYLE.lp_style_like_button == 1) {
    $(".lp_footer .lp_icons").append("\n            <a href=\"#\" class=\"lp_interactive_btn like\"></a>\n            <span>&nbsp;&nbsp;</span>\n        ");
  }

  if (LP_STYLE.lp_style_love_button == 1) {
    $(".lp_footer .lp_icons").append("\n            <a href=\"#\" class=\"lp_interactive_btn love\"></a>\n        ");
  }

  if (LP_STYLE.lp_style_dark_light_mode_button == 1) {
    $(".lp_footer .lp_icons").append("\n            <div class=\"toggle-btn\" id=\"_1st-toggle-btn\">\n                <input class=\"not_uniform comparator\" data-no-uniform=\"true\" type=\"checkbox\" checked>\n                <span></span>\n            </div>\n        ");
  }

  $('.lp_side_chat .close').click(function (e) {
    e.preventDefault();
    $(this).toggleClass('closed');
    $('.lp_side_chat_container').toggleClass('closed');
  });
  $('.lp_side_chat').on('click', '.lp_action.open', function (e) {
    e.preventDefault = true;
    e.returnValue = true;
    $(this).removeClass('open');
  });
  $('.lp_side_chat').on('click', '.lp_action', function (e) {
    e.preventDefault();
    $(this).toggleClass('open');
  });
  $('a.lp-liked-btn').on('click', function (e) {
    var p = $(this);
    $.ajax({
      type: 'POST',
      dataType: 'json',
      url: baseUri + 'index.php',
      data: {
        ajax: 1,
        controller: 'page',
        fc: 'module',
        module: 'lnk_livepromo',
        action: 'setLiked',
        id_product: p.data('id_product'),
        actionProduct: p.data('action')
      }
    }).fail(function (jqXHR, textStatus) {
      console.log(jqXHR);
      console.log(textStatus);
    }).success(function (data) {
      p.toggleClass('liked');
    });
  });
  $('.lp_comment_info_container').on('focus', function () {});
  $('.lp_comment_info_container').click(function () {
    $(this).toggleClass('show');
    $('.lp_comment_info_container').each(function () {
      $(this).removeClass('show');
    });
    $(this).addClass('show');
  }); // Lance particles 

  chat_show_particales();
  $('.lp_side').on('click', '.close_promo', function () {
    $promo = $(this).parents('.lp_promo');
    $promo.addClass('hide_promo');
    counter--;
    setTimeout(function () {
      $promo.remove();
    }, 500);
  }); // $('.lp_side').on('click', '.code', function (e) { 
  //     e.preventDefault();
  //     copyToClipboard($(this));
  // });
  // Listen for a click on the button 

  $("#_1st-toggle-btn").on("click", function () {
    // If the user's OS setting is dark and matches our .dark-mode class...
    if (prefersDarkScheme.matches) {
      // ...then toggle the light mode class
      document.body.classList.toggle("dark-mode");
      document.body.classList.toggle("light-mode"); // ...but use .dark-mode if the .light-mode class is already on the body,

      var theme = document.body.classList.contains("light-mode") ? "light" : "dark";
    } else {
      // Otherwise, let's do the same thing, but for .dark-mode
      document.body.classList.toggle("dark-mode");
      document.body.classList.toggle("light-mode");
      var theme = document.body.classList.contains("dark-mode") ? "dark" : "light";
    }

    var theme = "dark"; // Finally, let's save the current preference to localStorage to keep using it

    localStorage.setItem("theme", theme);
  });
  $('body').tooltip({
    selector: '.lp-liked-btn-2.not-logged',
    trigger: 'click',
    title: 'You should be connected',
    placement: 'top'
  }); // Tooltip

  $('body').tooltip({
    selector: '.code_promo',
    trigger: 'click',
    title: 'Copy to clipboard',
    placement: 'top'
  });
  $('.code_promo').tooltip("show");
  var clipboard = new ClipboardJS('.code_promo');
  clipboard.on("success", function (e) {
    $(e.trigger).attr('data-original-title', 'Copied!').tooltip("show").on(".tooltip", function (event) {
      setTimeout(function () {
        $(event.target).tooltip("hide");
      }, 2000);
    }).attr('data-original-title', 'Copy to clipboard');
  }); // Check for dark mode preference at the OS level

  var prefersDarkScheme = window.matchMedia("(prefers-color-scheme: dark)"); // Get the user's theme preference from local storage, if it's available

  var currentTheme = localStorage.getItem("theme"); // If the user's preference in localStorage is dark...

  if (currentTheme == "dark") {
    // ...let's toggle the .dark-theme class on the body
    document.body.classList.toggle("dark-mode"); // Otherwise, if the user's preference in localStorage is light...
  } else if (currentTheme == "light") {
    // ...let's toggle the .light-theme class on the body
    document.body.classList.toggle("light-mode");
  }

  document.body.classList.add("dark-mode");
  var root = document.documentElement;
  var main_chat_color = LP_STYLE.lp_style_main_color ? LP_STYLE.lp_style_main_color : '#e84667';
  console.log(LP_STYLE);
  hexToHSL(main_chat_color, root);
});

function hexToHSL(H, root) {
  // Convert hex to RGB first
  var r = 0,
      g = 0,
      b = 0;

  if (H.length == 4) {
    r = "0x" + H[1] + H[1];
    g = "0x" + H[2] + H[2];
    b = "0x" + H[3] + H[3];
  } else if (H.length == 7) {
    r = "0x" + H[1] + H[2];
    g = "0x" + H[3] + H[4];
    b = "0x" + H[5] + H[6];
  } // Then to HSL


  r /= 255;
  g /= 255;
  b /= 255;
  var cmin = Math.min(r, g, b),
      cmax = Math.max(r, g, b),
      delta = cmax - cmin,
      h = 0,
      s = 0,
      l = 0;
  if (delta == 0) h = 0;else if (cmax == r) h = (g - b) / delta % 6;else if (cmax == g) h = (b - r) / delta + 2;else h = (r - g) / delta + 4;
  h = Math.round(h * 60);
  if (h < 0) h += 360;
  l = (cmax + cmin) / 2;
  s = delta == 0 ? 0 : delta / (1 - Math.abs(2 * l - 1));
  s = +(s * 100).toFixed(1);
  l = +(l * 100).toFixed(1);
  root.style.setProperty('--hue', h);
  root.style.setProperty('--saturation', s + "%");
  root.style.setProperty('--light', l + "%");
  return "hsl(" + h + "," + s + "%," + l + "%)";
}

function copyToClipboard(element) {
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val($(element).text()).select();
  document.execCommand("copy");
  $temp.remove();
}

function getRndIntiger(min, max) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

function bodyOverflowToggle(x) {
  if (x.matches) {
    // If media query matches
    $('body').toggleClass('overflow-hidden');
  }
}