window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"16"},"share":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];

$('.menubtn').click(function(){
	$(this).toggleClass('on');
	$('.menudiv').toggle();
})

if($('.desc').height()<=120){
	$('.desc .more').hide();
} else {
	$('.desc .more').show();
	$('.desc').css({'height':'120px'});
}
$('.desc .more').click(function(){
	var $str = $(this).text();
	if($str=='展开>>'){
		$(this).text('收起<<');
	$('.desc').css({'height':'auto'});
	} else {
		$(this).text('展开>>');
		$('.desc').css({'height':'120px'});
	}
})

//AddFavorite
function AddFavorite(sURL, sTitle){
	try{window.external.addFavorite(sURL, sTitle);}
	catch (e){
		try{window.sidebar.addPanel(sTitle, sURL, "");}
		catch (e){alert("加入收藏失败，请使用Ctrl+D进行添加");}
	}
	}

//SetHome
function SetHome(obj,vrl){try{obj.style.behavior='url(#default#homepage)';obj.setHomePage(vrl);}catch(e){if(window.netscape){try{netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");}catch (e) {alert("此操作被浏览器拒绝！\n请在浏览器地址栏输入“about:config”并回车\n然后将 [signed.applets.codebase_principal_support]的值设置为'true',双击即可。");}var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);prefs.setCharPref('browser.startup.homepage',vrl);}}}

/*banneer*/
jQuery(".focusBox").slide({ mainCell:".pic",effect:"left", autoPlay:true, delayTime:300});

/*picScroll*/
jQuery(".picScroll").slide({ mainCell:".picList", effect:"left",vis:5, pnLoop:false, scroll:5, autoPage:true});

//gotop
var gotoTop = { fixed: "#costom", id: "#costom", clickMe : function(){ $('html,body').animate({scrollTop : '0px'},{ duration:500}); }, toggleMe : function() { if($(window).scrollTop() == 0) { $(this.fixed).stop().animate({'opacity': 0}, "slow"); } else { $(this.fixed).stop().animate({'opacity': 1}, "slow"); } }, init : function() { $(this.fixed).css('opacity', 0); $(this.id).click(function(){ gotoTop.clickMe(); return false; }); $(window).bind('scroll resize', function(){ gotoTop.toggleMe(); }); } }; gotoTop.init();
