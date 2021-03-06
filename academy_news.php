<?php
	require 'LIB_http.php';
	require 'LIB_parse.php';
	require 'db.connection.php';
	
	function parse_html($contents)
	{
		$result = array();
		$web_page = $contents; //This is html contents.
		
		//checking contents are not '數據載入中.....'
		if(mb_stristr($web_page,'數據'))
		{
			return $result;
		}
		
		$web_page = return_between($web_page,'<div class="md_middle"><div class="mm_03"><div class="mm_02"><div class="mm_01">','</div></div></div></div>',EXCL);
		$web_page = return_between($web_page,'<table class="baseTB listTB list_TABLE hasBD hasTH" cellspacing="0" cellpadding="0" border="0" width="100%" summary="">','</table>',EXCL);
		//$get_thead = return_between($web_page,'<thead>','</thead>',EXCL);
		$get_tbody = return_between($web_page,'<tbody>','</tbody>',EXCL);
		$get_date = parse_array($get_tbody,'<td width="8%" nowrap="nowrap">','</td>'); //日期
		$get_title = parse_array($get_tbody,'<a ','</a>'); //標題
		
		$get_date_len = count($get_date);
		$get_title_len = count($get_title);
		
		$res_count = 0;
		for($get_date_count=0;$get_date_count<$get_date_len;$get_date_count++)
		{
			$result[$res_count]['date'] = trim(return_between($get_date[$get_date_count],'<td width="8%" nowrap="nowrap">','</td>',EXCL));
			$res_count++;
		}
		
		$res_count = 0;
		for($get_title_count=0;$get_title_count<$get_title_len;$get_title_count++)
		{
			$result[$res_count]['title'] = get_attribute($get_title[$get_title_count],$attribute = 'title');
			$result[$res_count]['link'] = get_attribute($get_title[$get_title_count],$attribute = 'href');
			$res_count++;
		}
		
		return $result;
	}
	
	function parse_enews_html($contents)
	{
		$result = array();
		$web_page =trim($contents);
		//check contents are  included 數據載入中
		if(mb_stristr($web_page,'數據'))
			return $result;
		
		$web_page = return_between($web_page,'<table summary="list" cellspacing="0" cellpadding="0" border="0" width="100%" class="baseTB listSD">','</table>',EXCL);
		$get_div = parse_array($web_page,'<div class="h5">','</div>');
		
		$res_count = 0;
		foreach($get_div as $val)
		{
			$val = return_between($val,'<div class="h5">','</div>',EXCL);
			$get_date = return_between($val,'<span class="date float-right">','</span>',EXCL); //日期
			$get_title = parse_array($val,'<a ','</a>'); //標題
			$get_title_len = count($get_title);
			
			$result[$res_count]['date'] = trim($get_date,'ght\" > []');
			
			for($get_title_count=0;$get_title_count<$get_title_len;$get_title_count++)
			{
				$result[$res_count]['title'] = get_attribute($get_title[$get_title_count],$attribute = 'title');
				$result[$res_count]['link'] = get_attribute($get_title[$get_title_count],$attribute = 'href');
			}
			
			$res_count++;
		}
		
		return $result;
	}
	
	$web_page = file_get_contents("enews.html");
	$result = parse_enews_html($web_page);
	if(count($result)==0)
	{
		file_put_contents('error_log.txt','This error occured in '.date('Y-m-d').' and  This site aliased name is : '.$key.'\r\n',FILE_APPEND);
	}
	else
	{
		echo json_encode($result);
	}
?>
