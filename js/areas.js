jQuery(function($){
	// generate a slug when the user types a title in
	pyro.generate_slug('input[name="title"]', 'input[name="slug"]');
	
    $('.radio-banner-area').change(function () {
        $('[name=cmd]').val('set_default')
        $('#f-banner').submit();
    });
    
    $(":checkbox[name='blah']").click(function() {
        $("#theform").submit();      
    });
});