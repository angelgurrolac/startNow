$("#state").change(function(event){
	$.get("towns/" + event.target.value +"", function(response, state){
		$("#town").empty();
		for(i=0; i<response.length; i++) {
			$("#town").append("<option value='"+response[i].id+"'>"+ response[i].name+ "</option>");
		}
	});
});