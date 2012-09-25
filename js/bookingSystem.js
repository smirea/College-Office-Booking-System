(function(){

  $.bookingSystem = {
    options : {
      ajaxFile    : 'ajax.php',
      type        : '',
      checkLogin  : 30 * 1000
    }
  };
  
  $.fn.bookingSystem = function( options ){

    var opt = {};
    
    $.extend( opt, $.bookingSystem.options, options );
    
    return this.each(function(){

      init( $(this), opt );
      
      getRecent();
      
    });

    var options = {
      
    };
    
    function init( obj, opt ){
      options = {
        sql     : {
          orderBy   : "booked, returned",
          max     : 100
        }
      };
      data = {
        images  : {
          ajax  : '<img src="images/ajax.gif" height="18" title="Loading data..." />'
        }
      };
      
      var com = {
        self    : $(this),
        opt     : opt,
        timeout : {
          login : null
        }
      };
    
      var date  = new Date();
      date      = date.getDate()+'.'+(date.getMonth()+1)+'.'+date.getFullYear();

      obj.html(getStructure({
        date  : date
      }));

      $('a[href=#]', obj).attr('href', 'javascript:void(0)');
      $('a', obj).live('focus.blur', function(){ $(this).blur(); });

      $('#lastItems tr', obj).live('mouseover.changeColor', function(){
          $(this).addClass( 'selectedTR' );
          addSmallTooltip( $(this).find('.TD_booked') );
          addSmallTooltip( $(this).find('.TD_returned') );
        }).live('mouseout.changeColor', function(){
          var data = $(this).data('hover');
          $(this).removeClass( 'selectedTR' );
          $(this).find('.smallTooltip').remove();
      });
      
      if( opt.checkLogin ){
        com.timeout.login = setTimeout( function(){ checkLogin( com ); }, opt.checkLogin );
      }
        
      $('#openHelp', obj).bind ('click.openHelp', function(){
        var help = getHelp();
        help.dialog({
          modal     : true,
          resizable : false,
          title     : 'How to use the booking system',
          minWidth  : 600,
          maxWidth  : 600,
          maxHeight : 600,
          show      : 'bounce',
          hide      : 'explode'
        });
      });

      $('#help #closeMe', obj).bind('click.closeMe', function(){
        $(this).parent().fadeOut();
      });

      $('#maxNumber', obj).clone().attr('id', '').appendTo( $('#insertMaxNumber', obj) );

      options.sql.max = $('#maxNumber', obj).val();
      $('#s_user', obj).trigger('click.sort');

      $('#submitBooking', obj).bind("click.submitBooking", function(){
          $.get(opt.ajaxFile, {
              action    : 'book',
              type      : opt.type,
              item      : $('#item', obj).val(),
              booked    : formatDate($('#booked', obj).val()),
              returned  : formatDate($('#returned', obj).val()),
              phone     : $('#phone', obj).val(),
              user      : $('#user', obj).val(),
              email     : $('#email', obj).val()
            }, function(r){
              if( checkJSON( r ) ){
                getRecent();
                $('#item, #phone, #user, #email, #returned', obj).val('');
              }
          });
        });

        $('.action_delete', obj).live('click.deleteBooking', function(){
          if(confirm('Are you really really sure you want to delete this entry?')){
            $.get(opt.ajaxFile, {
                action  : 'delete',
                id      : $(this).attr('id')
              }, standardResponse
            );
          }
        });

        $('.action_return', obj).live('click.returnBooking', function(){
          var date = $(this).parent().parent().find('.TD_returned').find('.value');

          if(!date.html().length) {
            date.html( data.images.ajax );
            $.get(opt.ajaxFile, {
                action  : 'return',
                id      : $(this).attr('id')
              }, standardResponse
            );
          } else {
            if(confirm('Are you really really sure that this person did not return the item?')){
              $.get(opt.ajaxFile, {
                  action          : 'update',
                  'VALUES[returned]'  : '',
                  id              : $(this).attr('id')
                }, standardResponse
              );
            }
          }
        });

        $('.action_edit', obj).live('click.editBoodking', function(){
          $(this).data('otherItem', {
              'class' : $(this).attr('class'),
              'title' : $(this).attr('title'),
              'src'   : $(this).attr('src')
            }).attr({
              'class' : 'action action_save',
              'title' : 'Save the thiscussion',
              'src'   : 'images/icon_save.png'
            });
          $('.TD_user, .TD_phone, .TD_booked, .TD_item, .TD_returned, .TD_email', $(this).parent().parent()).each(function(){
            var cPos1 = this.className.indexOf('TD_')+3;
            var cPos2 = this.className.indexOf(' ', cPos1)+1;
            cPos2 = cPos2 > cPos1 ? cPos2 : this.className.length;
            $(this).html(
              $('#'+this.className.slice(cPos1, cPos2), obj)
                .clone()
                .val( $(this).find('.value').html() )
                .css('visibility', 'visible')
                .attr('disabled', false)
            );
          });
        });

        $('.action_save', obj).live('click.saveBooking', function(){
          params = {
            action  : 'update',
            id      : $(this).attr('id')
          };
          //$(this).attr($(this).data('otherItem'));
          $('.TD_user, .TD_phone, .TD_booked, .TD_item, .TD_returned, TD_email', $(this).parent().parent()).each(function(){
            var cPos1 = this.className.indexOf('TD_')+3;
            var cPos2 = this.className.indexOf(' ', cPos1)+1;
            cPos2 = cPos2 > cPos1 ? cPos2 : this.className.length;
            var p   = 'VALUES['+this.className.slice(cPos1, cPos2)+']';
            params[p] = $(this).find('input:text').val();
            $(this).html( $(document.createElement('span')).addClass('value').html( params[p] ) );
          });
          $.get(opt.ajaxFile, params, standardResponse);
        });

        $('.action_remind', obj).live('click.remindBooking', function(){
          if( true ){
            alert( 'Mail function disabled for now' );
          } else {
            showPopup();
          }
        });
        
        /*
        $('#tPopup #submit').bind('click.sendMail', function(){
          $.get(opt.ajaxFile, {
              action          : $('#tPopup #action').val(),
              'MAIL[to]'      : $('#tPopup #to').val(),
              'MAIL[from]'    : $('#tPopup #from').val(),
              'MAIL[subject]' : $('#tPopup #subject').val(),
              'MAIL[message]' : $('#tPopup #message').val()
            }, function(r){
              if( checkJSON(r) ){
                $('#tPopup #closePopup').click();
              }
          });
        });
        */
        
        $('.filter', obj).bind('keyup.filter', function(){

          $('#filterTR td:first img', obj).attr('src', 'images/ajax.gif');

          get_options = { action:'filter', max:options.sql.max };
          fail      = true;
          $('.filter', obj).each(function(){
            if(this.value.length > 0) {
              get_options['FILTER['+this.id.slice(2)+']'] = this.value;
              fail = false;
            }
          });
          if(fail) {
            $('#filterTR td:first img', obj).attr('src', 'images/icon_search.png');
            getRecent();
            return;
          }
          $.get(opt.ajaxFile, get_options, function(r){
            if( checkJSON(r) ){
              updateTable(r);
              $('#filterTR td:first img', obj).attr('src', 'images/icon_search.png');
            }
          });
        });

        $('#maxNumber', obj).bind('change.setMaxRecentItems', function(){
          resetFilters();
          options.sql.max = $(this).val();
          getRecent();
        });

        $('.sort', obj).bind('click.sort', function(){
          var order = this.id.slice(2);
          if(!$(this).find('#sort_arrow').hasClass('sort_descending') && $(this).find('#sort_arrow').hasClass('sort_ascending')){
            $('#main #sort_arrow').removeClass('sort_ascending').addClass('sort_descending');
            order += ' DESC';
          } else {
            $(this).append( $('#main #sort_arrow').addClass('sort_ascending').removeClass('sort_descending') );
          }
          options.sql.orderBy = order;

          $.get(opt.ajaxFile, {
              action  : 'select',
              max     : options.sql.max,
              orderBy : options.sql.orderBy
            }, function(r){
              if( checkJSON(r) ){
                updateTable(r);
                resetFilters();
              }
          });
        });
      
        $('#booked, #returned, #f_booked, #f_returned', obj)
          //.attr( 'readonly', 'readonly' )
          .button()
          .datepicker({
            firstDay          : 1,
            dateFormat        : 'dd.mm.yy',
            showButtonPanel   : true,
            onSelect          : function( dateText, inst ){
              $(this).val( dateText ).trigger( 'keyup' );
            }
          })
          .bind( 'click.showDatePicker', function(){
            $(this).datepicker('show');
          });
        
        $('#user', obj).autocomplete({
          autofocus : true,
          create    : function( e, data ){
            var widget = $(this).autocomplete('widget');
            var elem = $(document.createElement('div'));
            elem.insertAfter( widget )
              .addClass( 'face' )
              .hide()
              .css( 'position', 'absolute' );
            $(this).data('faceContainer', elem);
          },
          focus     : function( e, data ){
            var widget = $(this).autocomplete('widget');
            $(this).data('faceContainer')
              .css({
                left      : widget.offset().left + widget.outerWidth(),
                top       : widget.offset().top
              })
              .fadeIn()
              .html( faceTemplate(data.item.full) );
          },
          close : function(){
            $(this).data('faceContainer').fadeOut();
          },
          select  : function( e, data ){
            $('#account', obj).val( data.item.full.account );
            $('#phone', obj).val( data.item.full.phone );
            $('#email', obj).val( data.item.full.email );
            $('#item', obj).focus();
          },
          source    : function( request, response ){
            $.get( opt.ajaxFile,{
                action  : 'autoComplete',
                src     : request.term
              }, function( data ){
                response($.map( data.records, function( item ){
                  return {
                    label : item.fname+' '+item.lname,
                    value : item.fname+' '+item.lname,
                    full  : item
                  }
                }));
            });
          }
        });
    }
    
    function formatDate (dateString) {
      if (/^[0-9]{2}\.[0-9]\.[0-9]{4}$/.test(dateString)) {
        var arr = dateString.split('.');
        arr[1] = '0' + arr[1];
        dateString = arr.join('.');
      }
      return dateString;
    }

    function addSmallTooltip( obj ){
      var title = obj.attr('title');
      if( title && title.length > 0 ){
        obj.append(
          $(document.createElement('div'))
            .addClass('smallTooltip')
            .html( title )
        );
      }
    }
    
    function checkLogin( com ){
      $.get( com.opt.ajaxFile, {
          action  : 'checkLogin'
        }, function( r ){
          if( r.error ){
            M( r.error, 'warn' );
          } else if( r._loggedOut === true ){
            location.reload( true );
          }
        });
      com.timeout.login = setTimeout( function(){ checkLogin( com ); }, com.opt.checkLogin );
    }
    
    function standardResponse(r){
      if( checkJSON(r) ){
        getRecent();
        resetFilters();
      }
    }

    function getRecent(){
      $('#lastItems').html('<tr><td colspan="6">'+data.images.ajax+'</td></tr>');
      $.get(opt.ajaxFile, {
          action  : 'select',
          max   : options.sql.max,
          orderBy : options.sql.orderBy
        }, function(r){
          if( checkJSON(r) ){
            updateTable(r);
          }
      });
    }

    function resetFilters(){
      $('.filter').val('');
    }
    
    function dateFormat (timestamp, length) {
      length = length || 2;
      var z = function prependZero (no) {
        return ('0' + no).slice(-length);
      };
      var d = new Date(timestamp);
      return z(d.getDate())+'.'+z(d.getMonth())+'.'+d.getFullYear();
    }

    function updateTable(r){
      var h = $();
      var c = 0;
      for(var i=0; i<r.length; ++i){
        
        var checkOutBy = {
          //innerHTML:dateFormat(Number(r[i]['timestamp']) * 1000),
          innerHTML: r[i]["booked"],
          title: r[i]["checkOutBy"]
        };
        var checkInBy = r[i]["returned"].length > 0 ? {title:r[i]["checkInBy"]}: '';
        r[i]['number'] = ++c;
        
        var structure = [
          'number',
          'user',
          'phone',
          'email',
          'item',
          [ 'booked', checkOutBy ],
          [ 'returned', checkInBy ]
        ];
        
        var tr = $(document.createElement('tr')).addClass('tr'+(i%2));
        
        for( var j in structure ){
          var td = $(document.createElement('td'));
          var name = '';
          var value = null;
          if( typeof structure[j] != 'string' ){
            name = structure[j][0];
            if (structure[j][1].innerHTML) {
              value = structure[j][1].innerHTML;
              delete structure[j][1].innerHTML;
            }
            td.attr( structure[j][1] );
          } else {
            name = structure[j];
          }
          value = value || r[i][name];
          td
            .addClass( 'TD_'+name )
            .html( '<span class="value">'+value+'</span>' );
          tr.append( td );
        }
        
        tr.append(
          '<td>'+
            '<img src="images/icon_return.png" class="action action_return" id="'+r[i]["id"]+'" title="Set return-date to today" width="18" height="18" />'+
            '<img src="images/icon_edit.png" class="action action_edit" id="'+r[i]["id"]+'" title="Edit this booking" width="18" height="18" />'+
            /*'<img src="images/icon_remind.png" class="action action_remind" id="'+r[i]["id"]+'" title="Send an email reminder" width="18" height="18" />'+*/
            '<img src="images/icon_delete.png" class="action action_delete" id="'+r[i]["id"]+'" title="Delete this booking" width="18" height="18" />'+
          '</td></tr>'
        );
        
        h = h.add( tr );
      }
      $('#lastItems').html(h);
    }
    
  };

  function checkJSON( r ){
    if( typeof r == 'object' ){
      if( !r.error ){
        return true;
      } else {
        M( r.error, 'warn' );
      }
    } else {
      M( r, 'warn' );
    }
    return false;
  }
  
  function getStructure( opt ){
    return ''+
    '<table cellspacing="0" cellpadding="0" id="bookingTable">'+
      '<thead>'+
        '<tr>'+
          '<td align="center"><a href="#" id="s_id" class="sort">#<span id="sort_arrow" class="sort_ascending">&nbsp;</span></a></td>'+
          '<td style="text-indent:5px"><a href="#" id="s_user" class="sort">Name</a></td>'+
          '<td align="center"><a href="#" id="s_phone" class="sort">Phone</a></td>'+
          '<td><a href="#" id="s_email" class="sort">Email</a></td>'+
          '<td style="text-indent:5px"><a href="#" id="s_item" class="sort">What?</a></td>'+
          '<td align="center"><a href="#" id="s_booked" class="sort">When?</a></td>'+
          '<td align="center"><a href="#" id="s_returned" class="sort">Returned?</a></td>'+
          '<td align="center">Actions</td>'+
        '</tr>'+
      '</thead>'+
      '<tr id="newItemTR">'+
        '<td><img src="images/icon_help.png" id="openHelp" title="Open the help window" height="22" style="cursor:pointer" /></td>'+
        '<td><input tabindex="1" type="text" id="user" size="15" /></td>'+
        '<td><input tabindex="1" type="text" id="phone" size="3" maxlength="4" /></td>'+
        '<td><input tabindex="1" type="text" id="email" size="24" /></td>'+
        '<td><input tabindex="1" type="text" id="item" /></td>'+
        '<td><input tabindex="1" type="text" id="booked" value="'+opt.date+'" size="10" /></td>'+
        '<td><input tabindex="1" type="text" id="returned" class="lockedInput" value="" size="8" maxlength="10" /></td>'+
        '<td align="center">'+
          '<input type="button" tabindex="1" id="submitBooking" value="Book" />'+
        '</td>'+
      '</tr>'+
      '<tbody id="lastItems">'+
        '<tr><td><img src="images/ajax.gif" height="20" title="Loading data..." /></td></tr>'+
      '</tbody>'+
      '<tr id="filterTR">'+
        '<td><img src="images/icon_search.png" height="22" /></td>'+
        '<td><input tabindex="1" type="text" id="f_user" class="filter" size="15" title="Add a user filter" /></td>'+
        '<td><input tabindex="1" type="text" id="f_phone" class="filter" size="3" maxlength="4" title="Add a phone filter" /></td>'+
        '<td><input tabindex="1" type="text" id="f_email" class="filter" size="24" title="Add an email filter" /></td>'+
        '<td><input tabindex="1" type="text" id="f_item" class="filter" title="Add an item filter" /></td>'+
        '<td><input tabindex="1" type="text" id="f_booked" class="filter" size="10" title="Add a booked-date filter" /></td>'+
        '<td><input tabindex="1" type="text" id="f_returned" class="filter" size="8" maxlength="10" title="Add a returned-date filter" /></td>'+
        '<td align="center" title="The maximum number of elements to display">'+
          '<select tabindex="1" id="maxNumber">'+
            '<option>8</option>'+
            '<option selected="selected">16</option>'+
            '<option>32</option>'+
            '<option>64</option>'+
            '<option>128</option>'+
            '<option>256</option>'+
            '<option>512</option>'+
            '<option>1024</option>'+
          '</select>'+
        '</td>'+
      '</tr>'+
    '</table>';
  }
  
  function getHelp(){
    var html = '<fieldset id="help">'+
      '<h4>Legend</h4>'+
      '<table>'+
        '<tr>'+
          '<td align="center"><img src="images/icon_return.png" height="22"></td>'+
          '<td>Sets the <b>returned date</b> to the current date. If the returned date is set'+
          'then clicking on this icon will remove it (in case of mistakes)'+
          '</td>'+
        '</tr>'+
        '<tr>'+
          '<td align="center"><img src="images/icon_edit.png" height="22"></td>'+
          '<td>Enables editing all the fields. When done, click on the '+
          '<b>save (<img src="images/icon_save.png" height="18">)</b> icon that appears in its place'+
          '</td>'+
        '</tr>'+
        '<tr>'+
          '<td align="center"><img src="images/icon_remind.png" height="22"></td>'+
          '<td>Opens a dialog box to send emails to this person (Note: mails to'+
          'Jacobs-University take a bit longer to deliver)'+
          '</td>'+
        '</tr>'+
        '<tr>'+
          '<td align="center"><img src="images/icon_delete.png" height="22"></td>'+
          '<td>Deletes the current booking (not recommended)</td>'+
        '</tr>'+
        '<tr>'+
          '<td align="center"><img src="images/icon_search.png" height="22"></td>'+
          '<td> Type in the fields next to this icon to add search filters</td>'+
        '</tr>'+
        '<tr>'+
          '<td align="center" id="insertMaxNumber"></td>'+
          '<td>Select how many entries to display</td>'+
        '</tr>'+
        '<tr>'+
          '<td align="center"><img src="images/icon_help.png" height="22"></td>'+
          '<td>Opens up <b>this</b> help block</td>'+
        '</tr>'+
      '</table>'+
    '</fieldset>';
    return $(html);
  }
  
  /************* COPY PASTED FROM JPEOPLE !!! ***********/
  
  function days_between(date1, date2) {
    var ONE_DAY = 1000 * 60 * 60 * 24;
    if( !date2 ){
      date2 = new Date();
      date2.setYear( 2000 + date1.getYear() % 100 );
      if( date2.getMonth() > date1.getMonth() || date2.getMonth() == date1.getMonth() && date2.getDate() > date1.getDate() ){
        date1.setYear( 2000 + date1.getYear() % 100 + 1 );
      }
    }
    date1.setHours( 0 );
    date1.setMinutes( 0 );
    date1.setSeconds( 0 );
    date2.setHours( 0 );
    date2.setMinutes( 0 );
    date2.setSeconds( 0 );
    var date1_ms = date1.getTime();
    var date2_ms = date2.getTime();
    var difference_ms = Math.abs(date1_ms - date2_ms);
    return Math.round(difference_ms/ONE_DAY);
  }

  function prettyBirthday( date ){
    var months = [ 'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December' ];
    var d = date.split('.');
    var a;
    var b;
    if( d.length == 2 && (a = parseInt(d[0], 10)) && (b = parseInt(d[1], 10)) && a >= 1 && a <=31 && b>=1 && b<=12 ){
      var p = '';
      switch( a % 10 ){
        case 1: p = 'st'; break;
        case 2: p = 'nd'; break;
        case 3: p = 'rd'; break;
        default: p = 'th';
      }
      var numDays   = days_between(new Date(2012,b-1,a));
      var daysLeft  = '';
      switch( numDays ){
        case 0: daysLeft = '<b style="color:blue">TODAY!</b>'; break
        case 1: daysLeft = '<b>Tomorrow!</b>'; break;
        default: daysLeft = numDays+' days left'
      }
      return a+'<sup>'+p+'</sup> of '+months[b-1]+
             '<span class="daysLeft"> ('+daysLeft+')</span>';
    } else {
      return date;
    }
  }
  
  function collegeIcon( college ){
    college = college.toLowerCase();
    var CI = {
      'mercator'    : '<span class="college-icon mercator">M</span>',
      'krupp'       : '<span class="college-icon krupp">K</span>',
      'college-iii' : '<span class="college-icon college-iii">C3</span>',
      'nordmetall'  : '<span class="college-icon nordmetall">N</span>',
    };
    if( ['mercator','krupp','college-iii','nordmetall'].indexOf( college ) > -1 ){
      return CI[ college ];
    }
    return '';
  }
  
  function faceTemplate( dataObject ){
    var data = {};
    $.extend( data, dataObject );
    var country   = 'No man\'s land :(';
    if( data.country ){
      country = data.country+' <img src="http://swebtst01.public.jacobs-university.de/jPeople//embed_assets/flags/'+data.country+'.png" alt="country" />';
    }
    if( data.college ){
      data.college = collegeIcon( data.college ) + ' ' + data.college;
    }
    if( data.phone && data.phone.length == 4 ){
      data.phone = '+49 421 200 <b>'+data.phone+'</b>';
    }
    if( data.email ){
      data.email = '<a href="mailto:'+data.email+'" title="Email '+data.fname+'">'+data.email+'</a>';
    }
    if( data.birthday ){
      data.birthday = prettyBirthday( data.birthday );
    }
    var attribs = [ 'college', 'email', 'phone', 'room', 'birthday' ];
    var template =
      '<div class="face">'+
        '<div class="header">'+
          '<table class="photo" cellspacing="0" cellpadding="0">'+
            '<tr><td><img src="http://swebtst01.public.jacobs-university.de/jPeople/image.php?id='+data.eid+'" alt="The photo" /></td></tr>'+
          '</table>'+
          '<div class="name">'+
            '<span class="fname" title="tag: fname">'+data.fname+'</span>, '+
            '<span class="lname" title="tag: lname">'+data.lname+'</span>'+
          '</div>'+
          '<div>'+
            '<span class="majorlong">'+data.majorlong+'</span> <br />'+
            '<span class="description">'+data.description+'</span>'+
          '</div>'+
          '<div class="clearBoth"></div>'+
        '</div>'+
        '<table class="body" cellpadding="1">';

        for( var i in attribs ){
          if( !data[attribs[i]] ){
            continue;
          }
          template += '<tr>'+
                        '<td class="infoCell"> '+(attribs[i].slice(0,1).toUpperCase()+attribs[i].slice(1))+' </td>'+
                        '<td><span class="'+attribs[i]+'">'+data[attribs[i]]+'</span></td>'+
                      '</tr>';
        }
        template += '</table>'+
                    '<div class="country">'+
                        country+
                      '</div>'+
                    '</div>'+
                  '</div>';
    return template;
  }
  
  /**
   * A Message function. checks to see if firebug is enabled
   * @param obj the object to output. If firebut is disable, will only work for: string, int, float
   * @param type log, info, err
  **/
  function M(obj, type){
    type = type ? type : "log";
    if(typeof console != 'undefined' && console != null){
      switch(type){
        case "log"  : console.log( obj ); break;
        case "info" : console.info( obj ); break;
        case "warn" : console.warn( obj ); break;
        case "err"  : console.err( obj ); break;
      }
    }
  }
  
})(jQuery);
