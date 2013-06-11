function setContentSize(){
	//$(".content").width($(window).width() - $(".leftMenu").width() - 30);
}

$(function(){
	var actual = "";
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
	    	
	    	$("#loader").removeClass("active");

	    	actual = '#' + payload.presenter;
	    	window.location.hash = '#' + payload.presenter;
	    	
	    	scrollTo(0, 0);
	    }
	});
	
	
	$(window).on('hashchange',function(event) {
		
		if(window.location.hash.length == 0) var hash = "#Homepage";
		else var hash = window.location.hash;
		
		
		if(actual != window.location.hash){
			
			var hash = location.hash.substring(1);
		    
		}
	});
	
	$('a.ajax, a.grid-ajax').live('click', function (event) {
		// loader
	    $("#loader").addClass("active");
	});
	
	// odesílání odkazů
	$('a.ajax').live('click', function (event) {
	    event.preventDefault();

	    var href = this.href;
	    $.get(href);
	    
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
	
	var nameSeo = window.location.hash.substring(1);
	//if (nameSeo.length == 0) nameSeo = "Homepage";

	$('*[data-nameseo="'+ nameSeo +'"]').click();
	
});