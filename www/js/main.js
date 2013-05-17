function setContentSize(){
	$(".content").width($(window).width() - $(".leftMenu").width() - 30);
}

$(function(){
	
	jQuery.ajaxSetup({
	    cache: false,
	    dataType: 'json',
	    success: function (payload) {
	        
	    	if (payload.redirect) {
                window.location.href = payload.redirect;
                return;
            }
	    	
	    	if (payload.snippets) {
	            for (var i in payload.snippets) {
	                $('#' + i).html(payload.snippets[i]);
	            }
	        }
	    }
	});

	// odesílání odkazů
	$('a.ajax').live('click', function (event) {
	    event.preventDefault();
	    $.get(this.href);
	});

	// odesílání formulářů
	$('form.ajax').live('submit', function (event) {
	    event.preventDefault();
	    $.post(this.action, $(this).serialize());
	});
	
	setContentSize();
	$(window).resize(function() {
	  setContentSize();
	});
	
	$(".close").click(function(){
		
		$(".settings-button").trigger("click");
	});
	
	$(".maximize").click(function(){
		
		$(".settings").toggleClass("maximized");
	});
	
	$(".messenger").click(function(){
		$(".messenger").toggleClass("selected");
	});
	
	$(".ajax").click(function(){
		$(".settings").removeClass("maximized");
		$("footer").removeClass("hide");
		
		$(".ajax").removeClass("selected");
		$(this).addClass("selected");
	});
	
	$(".new-page").live('click', function(){
		
		$(this).attr("target", "_blank");
	});
	
});

function createProductRow(item){
	
	if(typeof item.count =="undefined") item.count = 1;
	
	var row = "<div class='productRow'>"+item.value+"</div><table class='formProducts editing'><input type='hidden' name='products[]' value='"+item.id+"' /><br />";
	row += "<tr><td><input placeholder='Název' type='text' name='names[]' value='"+item.value+"' /><br />";
	row += "<tr><td>Dph: <select name='dph[]'><option "+(item.dph == "0" ? "selected" : "")+" value='0'>0%</option><option "+(item.dph == "15" ? "selected" : "")+" value='15'>15%</option><option "+(item.dph == "21" ? "selected" : "")+" value='21'>21%</option></select><br />";
	row += "<tr><td><input placeholder='Cena' type='text' name='prices[]' value='"+item.price+"' /><br />";
	row += "<tr><td><input placeholder='Počet' type='text' name='counts[]' value='"+item.count+"' /><br />";
	row += "<tr><td><input placeholder='Mj' type='text' name='mj[]' value='"+item.mj+"' /><br />";
	row += "<a href='#' onclick='$(this).parent().parent().parent().parent().prev().prev().remove(); $(this).parent().parent().parent().parent().remove(); return false;'>&times; Smazat položku<a/>";
	
	$("#frm-invoiceForm-productsAutoComplete").after(row);
}
