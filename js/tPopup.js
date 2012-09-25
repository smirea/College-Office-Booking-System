$(function(){

	$('#tPopup #message').tinymce({
		script_url									: 'tiny_mce/tiny_mce.js',
		theme 										: "advanced",
		cleanup_on_startup 						: true,
		width											: 503,
		height										: 300,
		skin 											: "o2k7",
		theme_advanced_toolbar_location		: "top",
		theme_advanced_statusbar_location 	: "bottom",
		theme_advanced_buttons1 				: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,"+
															"bullist,numlist,outdent,indent,|,forecolor,backcolor,|,link,unlink,image",
		theme_advanced_buttons2 				: "cut,copy,paste,undo,redo,cleanup,code,hr,removeformat,fontselect,fontsizeselect,help",
		theme_advanced_buttons3 				: "",
	});
	
	$('#tPopup #closePopup').click(function(){
		$('#tPopup').fadeOut();
	});
	
});

function showPopup(options){
	var def = {
		title			: 'Send an email reminder',
		description	: 'Remind someone that they have not returned something to the MCO',
		email			: '',
		subject		: 'Mercator College Office - Please return the stuff you took',
		message		: 'Please return back the stuff you took from the Mercator College Office as soon as possible',
		from			: 'Mercator College Office <collegeoffice@mercator-college.org>',
		action		: 'remind'
	}
	$.extend(def, options);
	
	$('#tPopup').fadeIn(600);
	$('#tPopup').css({
		position		: 'absolute',
		top			: $(window).scrollTop() + ($(window).height()-$('#tPopup').outerHeight()) / 2,
		left			: ($(window).width()-$('#tPopup').outerWidth()) / 2,
		zIndex		: 30000
	});
	$('#tPopup #title').html(def.title);
	$('#tPopup #description').html(def.description);
	$('#tPopup #email').val(def.email);
	$('#tPopup #subject').val(def.subject);
	$('#tPopup #message').val(def.message);
	$('#tPopup #action').val(def.action);
	$('#tPopup #from').val(def.from);
	$('#tPopup #sendEmail').attr('checked', true);
}
