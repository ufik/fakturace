function setContentSize(){
	$(".content").width($(window).width() - $(".leftMenu").width() - 30);
}

$(function(){
	
	$.nette.init(function (ajaxHandler) {
	    $('form.ajax').live('click', ajaxHandler);
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
