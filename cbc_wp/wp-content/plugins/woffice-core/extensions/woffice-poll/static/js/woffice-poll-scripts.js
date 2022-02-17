(function($){

	var $pollWrapper = $("#woffice_poll"),
		$pollWrapperParent = $pollWrapper.parent(),
		loader;

    $pollWrapper.find("button.btn").on('click', function(){
	 
    	loader = new Woffice.loader($pollWrapperParent);

        $pollWrapper.hide();
		
	});

    $(document).on('ready', function() {

        $pollWrapper.submit(pollAjaxSubmit);

        function pollAjaxSubmit(){

            var wofficePollData = $(this).serialize();

            $.ajax({
                type:"POST",
                url: Woffice.data.ajax_url.toString(),
                data: wofficePollData,
                success:function(data){
                    jQuery("#woffice_ajax_validation").html(data);
                },
                complete:function(){
                    loader.remove();
                }
            });

            return false;

        }
    });

})(jQuery);