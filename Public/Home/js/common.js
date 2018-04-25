window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"2","bdSize":"16"},"share":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];

//下一页异步加载
function loaddata(){
	$next_page = $page;
	$('.showpage .nextpage').addClass('loading').find('a').html('');
	if($page_count>$next_page){
		$.ajax({ 
			url: "/web/pagelist/",
			data:{    
				 catid : $catid,    
				 pagesize : $pagesize,  
				 nextpage : $next_page,  
			},   
			type:'post', 
			dataType:'json',
			success:function(data){ 
				if( data.msg == 0)
				{  
					$('.showpage .nextpage').removeClass('loading').find('a').html('点击加载更多');
					//alert($page_count+'---'+$next_page);
					if($page_count==($next_page+1))$('.showpage .nextpage').addClass('grey').html('加载完毕');
					$('.irankul').append(data.data);
					$_page++;
				}    
			}
		});
		$page++;
	}else{
		$('.showpage .nextpage').addClass('grey').html('加载完毕');
	}
}

$('.tabbox .tabtit span').each(function(i){
	$(this).mouseover(function(){
		$(this).addClass('on').siblings('span').removeClass('on');
		$('.tabdiv').eq(i).show().siblings('.tabdiv').hide();
	})
})

//当前导航状态
$('.nav span').show();
var $current_url = document.location.href;
var $hosturl = 'http://'+window.location.host+'/';
if ($current_url==$hosturl) {
	$('#nav li').eq(0).addClass('on');
} else {
	$('#nav li:gt(0)').each(function(){
		var $_url = $(this).find('a').attr('href');
		if($current_url.indexOf($_url)!==-1){
			$(this).addClass('on');
		}
	})
}
$('.nav').hover(function(){
	$(this).find('li').each(function(){
	var $left = (($(this).index()))*85+'px';
	if($(this).attr('class')=='on'){
		$('.nav span').css({'left':$left});
	}
	$(this).mouseover(function(e){
		var $pleft = $('.nav').offset().left;
		$leftwid = e.pageX-$pleft;
		$index = (Math.ceil($leftwid/85))-1;
		$left = $index*85+'px';
		$('.nav span').animate(
		{
			'left':$left
		},
		{
			duration : 500,
			easing : 'easeOutExpo',
			queue : false
		});
	})
})
},function(){
	var $_left = (($('.nav li.on').index()))*85+'px';
	$('.nav span').animate({'left':$_left});
})


/*排行榜*/
$('.ranktit span').each(function(i){
	$(this).mouseover(function(){
		$(this).addClass('on').siblings('span').removeClass('on');
		$('.irankul').eq(i).show().siblings('.irankul').hide();
	})
})

$('.irankul').each(function(){
	$(this).find('li:not(.nohover)').each(function(){
		$(this).mouseover(function(){
			$(this).addClass('on').siblings('li').removeClass('on');
		})
	})
})
if($('.linklist li').length<36){
	$('.linklist li.morelink').hide();
} else {
	$('.linklist li:gt(36):not(.morelink)').hide();
}
$('.linklist .morelink').click(function(){
	var $str = $(this).text();
	if($str=='更多>>'){
		$(this).text('收起<<');
		$('.linklist li').show();
	} else {
		$(this).text('更多>>');
		$('.linklist li:gt(36):not(.morelink)').hide();
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
jQuery(".banner").hover(function(){ jQuery(this).find(".prev,.next").stop(true,true).fadeTo("show",0.1) },function(){ jQuery(this).find(".prev,.next").fadeOut() });
jQuery(".banner").slide({ mainCell:".pic",effect:"fold", autoPlay:true, delayTime:300, trigger:"mouseover"});

/*cbanner*/
jQuery(".cbanner").hover(function(){ jQuery(this).find(".prev,.next").stop(true,true).fadeTo("show",0.9) },function(){ jQuery(this).find(".prev,.next").fadeOut() });
jQuery(".cbanner").slide({ mainCell:".pic",effect:"fold", autoPlay:true, delayTime:300, trigger:"mouseover"});

/*picScroll*/
jQuery(".picScroll").slide({ mainCell:".picList", effect:"left",vis:5, pnLoop:false, scroll:5, autoPage:true});



//gotop
var gotoTop = { fixed: "#costom", id: "#costom", clickMe : function(){ $('html,body').animate({scrollTop : '0px'},{ duration:500}); }, toggleMe : function() { if($(window).scrollTop() == 0) { $(this.fixed).stop().animate({'opacity': 0}, "slow"); } else { $(this.fixed).stop().animate({'opacity': 1}, "slow"); } }, init : function() { $(this.fixed).css('opacity', 0); $(this.id).click(function(){ gotoTop.clickMe(); return false; }); $(window).bind('scroll resize', function(){ gotoTop.toggleMe(); }); } }; gotoTop.init();