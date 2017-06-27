<!DOCTYPE html>
<html>
<head>
	<title>豆瓣图书</title>
	<script type="text/javascript" src="./jquery1.11.js"></script>
	<script type="text/javascript" src="./bootstrap/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="./bootstrap/css/bootstrap.min.css"/>
	<style type="text/css">
		body{
			
		    padding-top: 70px;
		    /*background:url('bk.jpg') 60% 100%  no-repeat fixed;*/
		}
		body:before {
		  content: ' ';
		  position: fixed;
		  z-index: -1;
		  top: 0;
		  right: 0;
		  bottom: 0;
		  left: 0;
		  background: url('bk.jpg') center 0 no-repeat;
		  background-size: 150% 120%;
		}
	</style>
</head>
<body>
		<nav class="navbar navbar-default navbar-fixed-top">
		  <div class="container-fluid">
		    <!-- Brand and toggle get grouped for better mobile display -->
		    <div class="navbar-header">
		      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
		        <span class="sr-only">Toggle navigation</span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		      </button>
		      <a class="navbar-brand" href="/douban">豆瓣图书<?php echo $catename ? ':'.$catename:''?></a>
		    </div>
			
		    <!-- Collect the nav links, forms, and other content for toggling -->
		    <div class="collapse navbar-collapse navbar-right" id="bs-example-navbar-collapse-1">
		      <ul class="nav navbar-nav">
		        <!-- <li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li> -->
		        <li><a href="/douban">首页</a></li>
		        <li class="dropdown sort_d">
		          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $sort_info[1]?> <span class="caret"></span></a>
		          <ul class="dropdown-menu" style="filter:alpha(Opacity=80);-moz-opacity:0.7;opacity: 0.7;">
		            <li><a v='1' href="javascript:;">评分&emsp;⬇️</li>
		            <li role="separator" class="divider"></li>
		            <li><a v='2' href="javascript:;">评论人数&emsp;⬇️</a></li>
		            <!-- <li role="separator" class="divider"></li> -->
		            <!-- <li><a v='3' href="javascript:;">推荐&emsp;⬇️</a></li> -->
		          </ul>
		        </li>
		      </ul>

		      <form class="navbar-form navbar-left search_name navbar-right">
		        <div class="form-group">
		          <input type="text" class="form-control" placeholder="Search" value="<?php echo $name?>">
		        </div>
		        <button type="submit" class="btn btn-default">Submit</button>
		      </form>
		      
		    </div><!-- /.navbar-collapse -->
		  </div><!-- /.container-fluid -->
		</nav>
	<div class="container" style="position: relative">
		

		<table class="table " style="width: 75% !important;float: left"><!-- table-striped table-bordered -->
			<?php foreach($list as $v):?>
				<tr>
					<td width="140px">
						<a href="<?php echo $v['href']?>" target="_blank">
							<img class="img-thumbnail" style="width: 140px !important;height:140px !important;" src="<?php echo $v['img']?>"/>
						</a>
					</td>
					<td>
						<h4><a style="color:gray" href="<?php echo $v['href']?>" target='_blank'>
						<?php echo $v['title'].(isset($v['catename']) ? '&nbsp;&nbsp;   ('.$v['catename'].')' : '')?></a></h4>
						<?php echo $v['author']?>/
						<?php echo $v['publisher'] ? $v['publisher'].'/':''?>
						<?php echo $v['publish_date'] ? $v['publish_date'].'/' : ''?>
						<?php echo $v['price']?>
						<p>评分:<?php echo $v['score']?>&emsp;评论人数:<?php echo $v['num']?></p>
						<p style="padding-right: 50px;"><?php echo $v['intro']?></p>
					</td>
				</tr>
			<?php endforeach;?>
		</table>
		<!-- margin-left:877.5px -->
		<div style="position:fixed;margin-left:60%;max-width:400px;height:auto;filter:alpha(Opacity=80);-moz-opacity:0.4;opacity: 0.4;">
			<div class="btn-group" role="group" aria-label="...">
				<?php foreach($cates as $k=>$v):?>
					<button type="button" class="btn btn-default pick_cate <?php if($k == $cate) echo 'btn-info'?> " cate_id="<?php echo $k?>"><?php echo $v?></button>
				<?php endforeach;?>
			</div>
		</div>
		<div style="clear: left"></div>
		<?php echo $page?>
	</div>
</body>
<script type="text/javascript">
	$('.pick_cate').unbind('click').bind('click',function(){
		var cate_id = $(this).attr('cate_id');
		window.location.href = "/douban/index.php?cate="+cate_id+"&sort=<?php echo $sort?>";
	})
	$('.search_name').find('button').unbind('click').bind('click',function(){
		var form = $('.search_name');
		var event = event || window.event;
		event.preventDefault(); // 兼容标准浏览器
		window.event.returnValue = false; // 兼容IE6~8
		search_name(form);
	})

	$('.search_name').find('input').unbind('keydown').bind('keydown',function(){
		if(event.keyCode == 13)
			search_name($('.search_name'));
	})

	$('.sort_d').find('ul li a').bind('click',function(){
		var v = $(this).attr('v');
		href = window.location.href;
		if(href.indexOf('?') > 0){
			href += '&sort='+v;
		}else{
			href += '?sort='+v;
		}
		window.location.href = href;
	});
	function search_name(form){
		var name = $(form).find('input').val();
		if(name.length > 0)
			window.location.href = "/douban/search.php?name="+name;
	}
</script>
</html>