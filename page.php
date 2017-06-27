<?php


function getPage($count,$page,$size){
	$url = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	if(strpos($url,'?') && !strpos($url,'page=')){
		$url .= "&page=".$page;
	}elseif(!strpos($url,'?')){
		$url .= "?page=".$page;
	}
	$html = '<nav aria-label="Page navigation" ><ul class="pagination pagination-lg">';

	$page_count = intval(ceil($count/$size));
	$pre = $page == 1 ? 'disabled' : '';
	$pre_href = $pre ? 'javascript:;' : str_replace('page='.$page,'page='.($page-1),$url);

	$next = $page == $page_count ? 'disabled' : '';
	$next_href = $next ? 'javascript:;' : str_replace('page='.$page,'page='.($page+1),$url);

	$html .= "<li class='{$pre}'><a href='{$pre_href}' aria-label='Previous'><span aria-hidden='true'>&laquo;</span></a></li>";
	$flag = true;
	for($i = 1;$i <= $page_count;$i++){
		if(abs($i - $page) < 4 || $i < 3 || abs($i - $page_count) < 3){
			if(abs($i-$page_count) == 2 && $page_count-$page > 6 && $page>6)
				$html .= "<li class='disabled'><a href='javascript:;'>...</a></li>";	
			$href = $i == $page ? 'javascript:;' : str_replace('page='.$page,'page='.$i,$url);
			$now = $i == $page ? 'disabled' : '';
			$html .= "<li class='{$now}'><a href='{$href}'>{$i}</a></li>";
			
		}else if($flag == true){
			$flag = false;
			$html .= "<li class='disabled'><a href='javascript:;'>...</a></li>";
		}else{
			continue;
		}
	}
	$html .= "<li class='{$next}'><a href='{$next_href}' aria-label='Next'><span aria-hidden='true'>&raquo;</span></a></li>";
	$html .= '</ul></nav>';
	return $html;
}