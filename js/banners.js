$(document).ready(function() {
    /*
    $("#fbanners").validate({
        showErrors: function(errorMap, errorList) {
            //$("#summary").html("Your form contains " + this.numberOfInvalids() + " errors, see details below.");
            this.defaultShowErrors();
          },
  
		rules: {
			title: "required",
            upload_img: "required",
            banner_link_type: "required"
		}
	});
    */


    jQuery(function($){

        $('#banner_link_type').change(function() {

             //alert('Value change to ' + $(this).attr('value'));
             switch ( $(this).attr('value'))
             {
                 case '0':
                     $('#li-banner-1').hide();
                     $('#li-banner-2').hide();
                     $('#li-banner-3').hide();

                 break;

                 case '1':
                     $('#li-banner-1').hide();
                     $('#li-banner-2').hide();
                     $('#li-banner-3').hide();

                 break;

                 case '2':
                     $('#li-banner-1').show();
                     $('#li-banner-2').hide();
                     $('#li-banner-3').hide();

                 break;

                 case '3':
                     $('#li-banner-1').hide();
                     $('#li-banner-2').show();
                     $('#li-banner-3').show();

                 break;
             }
        });
    });
});

function get_page_data(banner_id) 
{
    //console.log('banner_id='+banner_id );
    $("#td_"+banner_id).fadeIn(400).html('<img src="system/cms/themes/pyrocms/img/colorbox/loading.gif" align="absmiddle" alt="Loading"/>');
    $.ajax({
        type: "POST",
        url: "admin/banners/ajax_get_associated",
        data: {id: banner_id},
        cache: false,
        success: function(my_links){
            $("#td_"+banner_id).empty();
            var myArray = JSON.parse(my_links);
            myArray.forEach(function(entry) {
                $("#td_"+banner_id).append(entry+"<br/>");
            });
        }
    });
    return;
};



// based on FAQ module
(function($) {
    $(function() {
        do_sortable();
        /**
         * Shortcut click event
         */
        /*
        $('#shortcuts ul li a, a[rel=ajax], a.button').live('click', function(e) {
            e.preventDefault();
            var load_url = $(this).attr('href');
            remove_notification();
            load_content(load_url);
        });
        /**
         * Form submit events
         */
        /*
        $('form#banners, form#categories').live('submit', function(e) {
            e.preventDefault();
           var post_url = $(this).attr('action');
           var form_data = $(this).serialize();
           var form_id = $(this).attr('id');
           
           do_submit(post_url, form_data, form_id);
        });
        
        /**
         * Sortable
         */
        function do_sortable()
        {
            $('.banner-list tbody').sortable({
                start: function(event, ui) {
                    $('tr').removeClass('alt');
                },
                stop: function(event, ui) {
                    order = new Array();
                    $('td.action-to input').each(function(index) {
                        var banners_id = $(this).val();
                        order[index] = banners_id;
                    });
                    $.post(SITE_URL + 'admin/banners/update_order', { order : order }, function(data, response, xhr) {
                    
                    });
                }
            });
        }
        
        /**
         * Form submit handler
         */
        function do_submit(post_url, form_data, form_id)
        {
            var url = SITE_URL + 'admin/banners';
            if(form_id == 'categories')
            {
                url = SITE_URL + 'admin/banners/categories';
            }
            $.post(post_url, form_data, function(data, response, xhr) {
                 var obj = $.parseJSON(data);
                 if(obj.status == 'success')
                 {
                    load_content(url,obj.status,obj.message);
					//create_notification(obj.status, obj.message);
                 }
				 else
					create_notification(obj.status, obj.message);
            });
        }
        
        /**
         * add notification
         */
        function create_notification(type, message)
        {
            var notice = '<div class="alert '+ type +'">'+message+'</div>';
            remove_notification();
            $('#content-body').prepend(notice);
            $('.alert').fadeIn('normal');
        }
        
        /**
         * Remove notifications
         */
        function remove_notification()
        {
            $('.alert').fadeOut('normal', function() {
               $(this).remove(); 
            });
        }
        
        /**
         * Content switcher
         */
        function load_content(load_url,type,message)
        {
            $('#content-body').fadeOut('normal', function() {
               $(this).load(load_url, function(data, response, xhr) {
                    
					//handle answer ckeditor
					if(typeof CKEDITOR != 'undefined')
					{
						var editor = CKEDITOR.instances['answer'];
						if (editor)
						{
							editor.destroy(true);
						}
					}
					//init_ckeditor();
                    do_sortable();
					$(this).find('.table_action_buttons button').attr('disabled','disabled');
					if( typeof type != 'undefined' && typeof message != 'undefined' )
						create_notification(type,message);
						
                    $(this).fadeIn('normal');
               });
            });
        }
        
        /*
        function init_ckeditor()
        {
            $('textarea.wysiwyg-simple').ckeditor({
				toolbar: [
					 ['Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink']
				  ],
				width: '99%',
				height: 100,
				dialog_backgroundCoverColor: '#000',
				contextmenu: { options: 'Context Menu Options' }
			});
        }
        */
    });
})(jQuery);