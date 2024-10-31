// JavaScript Document
jQuery(function($){
	//alert(ni_nipv_ajax_object.ni_nipv_ajax_object_ajaxurl);
	
	$( "#frm_nipv_report" ).submit(function( e ) {
		$.ajax({
			
			url:ni_nipv_ajax_object.ni_nipv_ajax_object_ajaxurl,
			data: $(this).serialize(),
			success:function(response) {
				
				alert(JSON.stringify(response));
				$(".ajax_cog_content").html(response);
			},
			error: function(response){
				console.log(response);
				//alert("e");
			}
		}); 
		e.preventDefault();
	});
	
	//$( "#frm_cog_report" ).trigger( "submit" );
	
	
});