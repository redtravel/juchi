/**
 * A plugin for Framework7 to give the user a button for scrolling up
 *
 * @author www.timo-ernst.net
 * @license MIT
 */
Framework7.prototype.plugins.upscroller = function (app, globalPluginParams) {
  'use strict';
  
  var Upscroller = function (text) {
    var self = this,
      $$ = Dom7,
      curpage = $$('page-on-center').length>0?$('.page-on-center .page-content'):$('.page .page-content');
    
    var $$pages = $$('.page-content');
    var $$btn = $$('<div class="upscroller show"></div>');
    var $$body = $$('body');
    $$body.prepend($$btn);

    $$btn.click(function(event){
      event.stopPropagation();
      event.preventDefault();
      $(curpage).animate({scrollTop:0}, Math.round(curpage.scrollTop/4));
    });

    $$pages.scroll(function(event){
      var page = event.target;
      if (page.scrollTop < 400) {
        $$btn.addClass('show');
      }
      else {
        $$btn.removeClass('show');
      }
    });
    
    $$pages.each(function (i, page) {
      var pagename = $$(page).parent().attr('data-page');
      app.onPageBeforeAnimation(pagename, function (thepage) {
        curpage = $(thepage.container).children('.page-content').get(0);
        var scrollpos = curpage.scrollTop;
        if (scrollpos < 400) {
          $$btn.addClass('show');
        }
        else {
          $$btn.removeClass('show');
        }
      });
    });
    
    return this;
  };
  
  app.upscroller = function (text) {
    return new Upscroller(text);
  };
  
};