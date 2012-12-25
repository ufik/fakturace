function setContentSize(){
	$(".content").width($(window).width() - $(".leftMenu").width() - 30);
}

$(function(){
	
	$.nette.init();
	
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

var editor, html = '';

function createEditor() {
	
	//if ( editor )
		//return;
	
	// Create a new editor inside the <div id="editor">, setting its value to html
	var config = {};
	editor = CKEDITOR.appendTo( 'editor', config, html );
}

function removeEditor() {
	if ( !editor )
		return;

	// Retrieve the editor contents. In an Ajax application, this data would be
	// sent to the server or used in any other way.
	document.getElementById( 'editorcontents' ).innerHTML = html = editor.getData();
	document.getElementById( 'contents' ).style.display = '';

	// Destroy the editor.
	editor.destroy();
	editor = null;
}
