/**
 * Unicorn Admin Template
 * Diablo9983 -> diablo9983@gmail.com
**/
$(document).ready(function(){

	
	
	// === Sidebar navigation === //
	
	$('#sidebar a').click(function(e){
	var link=$(this).attr('href');
        e.preventDefault();
        $('#content').load(link);
        });
        
});
