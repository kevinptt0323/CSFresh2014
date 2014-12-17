// namespace
window.semantic = {
  handler: {}
};

// Allow for console.log to not break IE
if (typeof window.console == "undefined" || typeof window.console.log == "undefined") {
  window.console = {
    log  : function() {},
    info : function(){},
    warn : function(){}
  };
}
if(typeof window.console.group == 'undefined' || typeof window.console.groupEnd == 'undefined' || typeof window.console.groupCollapsed == 'undefined') {
  window.console.group = function(){};
  window.console.groupEnd = function(){};
  window.console.groupCollapsed = function(){};
}
if(typeof window.console.markTimeline == 'undefined') {
  window.console.markTimeline = function(){};
}
window.console.clear = function(){};

// ready event
semantic.ready = function() {

  // selector cache
  var

    $peek             = $('.peek'),
    $peekItem         = $peek.children('.menu').children('.item'),
    $peekSubItem      = $peek.find('.item .menu .item'),
    $waypoints        = $peek.closest('#main').find('.block').first().siblings('.block').addBack(),

    // alias
    handler
  ;


  // event handlers
  handler = {

    makeStickyColumns: function() {
      var
        $visibleStuck = $(this).find('.fixed.column .image, .fixed.column .content'),
        isInitialized = ($visibleStuck.parent('.sticky-wrapper').size() !== 0)
      ;
      if(!isInitialized) {
        $visibleStuck
          .waypoint('sticky', {
            offset     : 65,
            stuckClass : 'fixed'
          })
        ;
      }
      // apparently this doesnt refresh on first hit
      $.waypoints('refresh');
      $.waypoints('refresh');
    },

    movePeek: function() {
      if( $('.stuck .peek').size() > 0 ) {
        $('.peek')
          .toggleClass('pushed')
        ;
      }
      else {
        $('.peek')
          .removeClass('pushed')
        ;
      }
    },

    peek: function() {
      var
        $body     = $('html, body'),
        $header   = $(this),
        $menu     = $header.parent(),
        $group    = $menu.children(),
        $headers  = $group.add( $group.find('.menu .item') ),
        $waypoint = $waypoints.eq( $group.index( $header ) ),
        offset
      ;
      offset    = $waypoint.offset().top - 20;
      if(!$header.hasClass('active') ) {
        $headers
          .removeClass('active')
        ;
      }
      $menu
        .addClass('animating')
      ;
      $body
        .stop()
        .one('scroll', function() {
          $body.stop();
        })
        .animate({
          scrollTop: offset
        }, 500)
        .promise()
          .done(function() {
            $menu
              .removeClass('animating')
            ;
            $headers
              .removeClass('active')
            ;
            $header
              .addClass('active')
            ;
          })
        ;
    },

    peekSub: function() {
      var
        $body           = $('html, body'),
        $subHeader      = $(this),
        $header         = $subHeader.parents('.item'),
        $menu           = $header.parent(),
        $subHeaderGroup = $header.find('.item'),
        $headerGroup    = $menu.children(),
        $waypoint       = $('.block').eq( $headerGroup.index( $header ) ),
        $subWaypoint    = $waypoint.children('h3').eq( $subHeaderGroup.index($subHeader) ),
        offset          = $subWaypoint.offset().top - 40
      ;
      $menu
        .addClass('animating')
      ;
      if( !$header.hasClass('active') ) {
        $headerGroup
          .removeClass('active')
        ;
      }
      $subHeaderGroup
        .removeClass('active')
      ;
      $body
        .stop()
        .animate({
          scrollTop: offset
        }, 500, function() {
          $menu
            .removeClass('animating')
          ;
          $header
            .addClass('active')
          ;
          $subHeader
            .addClass('active')
          ;
        })
        .one('scroll', function() {
          $body.stop();
        })
      ;
      return false;
    }
  };


  $waypoints
    .waypoint({
      continuous : false,
      offset     : 100,
      handler    : function(direction) {
        var
          index = (direction == 'down')
            ? $waypoints.index(this)
            : ($waypoints.index(this) - 1 >= 0)
              ? ($waypoints.index(this) - 1)
              : 0
        ;
        $peekItem
          .removeClass('active')
          .eq( index )
            .addClass('active')
        ;
      }
    })
  ;
  $('body')
    .waypoint({
      handler: function(direction) {
        if(direction == 'down') {
          if( !$('body').is(':animated') ) {
            $peekItem
              .removeClass('active')
              .eq( $peekItem.size() - 1 )
                .addClass('active')
            ;
          }
        }
      },
      offset: 'bottom-in-view'
     })
  ;
  $peek
    .waypoint('sticky', {
      offset     : 85,
      stuckClass : 'stuck'
    })
  ;

  $peekItem
    .on('click', handler.peek)
    .children().on('click', function() { return false; });
  ;
  $peekSubItem
    .on('click', handler.peekSub)
  ;

};


// attach ready event
$(document)
  .ready(semantic.ready)
;
