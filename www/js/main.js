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
