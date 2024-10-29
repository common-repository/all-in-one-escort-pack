jQuery(document).ready(function($){
	$(".delprofile").click(function(){
		if(window.confirm('削除すると元に戻せませんがよろしいですか？')){
			id = $(this).attr("id"); 
			$('<input />').attr('type', 'hidden')
			 .attr('name', 'delete_id')
			 .attr('value', id)
			 .appendTo('#myForm');
			$('#myForm').submit();
		}
	});
});