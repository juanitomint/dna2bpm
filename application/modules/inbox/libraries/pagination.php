<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Pagination
 * 
 * This class handles pagination
 * 
 * @author Gabriel Fojo "The macho" <trialvd@gmail.com>
 * @date    Jun 16, 2014
 */
class Pagination extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->config('config');
        $this->load->library('parser');
        $this->base_url = base_url();
        

    }

    function Index($config) {
     	$default=array('url'=>$this->base_url,
    			'current_page'=>1,
    			'items_total'=>0,
    			'items_x_page'=>10,
    			'pagination_width'=>5,
    			'class_ul'=>"",
    			'class_a'=>"",
     			'ajax_target'=>'',
     			'pagination_always_visible'=>false
    	);

    	$params=array_merge($default,$config);
		extract($params);
		$class_link=$class_a;


    	$link=$url."/";
    	$paged['total_pages']=floor($items_total/$items_x_page);
    	if($items_total%$items_x_page)$paged['total_pages']++;


    	// MIN MAX RANGE
    	$actual=(int)$current_page;
    	while($actual%$pagination_width!=0){
    		$actual++;
    	}

    	$paged['min']=$actual-($pagination_width-1);
    	$paged['max']=$actual;
    	
    	$customData['pagination']='<ul class="pagination '.$class_ul.'">';
    	// Arrow Prev <
    	$class=($paged['min']==1)?('disabled'):('');
    	if($paged['min']==1){
    		$class="disabled";
    		$url="#";
    	}else{
    		$class="";
    		$back=$paged['min']-1;
    		$url=$link.$back;
    	}
    	$customData['pagination'].=" <li class='$class'><a href='$url' class='prev $class_link' data-target='$ajax_target'>&laquo;</a></li>";

    	// Pages Loop
    	for($i=$paged['min'];$i<=$paged['max'];$i++){
    	$class=($i==$current_page)?('active'):('');
    		if($i>$paged['total_pages']){
    			$class='disabled';
    			$url="#";
				$class_link="";
    		}else{
    			$url=$link.$i;
    		}  	    		
    	$customData['pagination'].="<li class='$class'><a href='$url' class='$class_link'>$i</a></li>";
    	}
    	
    	// Arrow Next >
    	$class=($paged['max']<$paged['total_pages'])?(''):('disabled');
    	if($paged['max']<$paged['total_pages']){
    	$class="";
    		$next=$paged['max']+1;
			$url = $link . $next;
		} else {
			$class = "";
			$class_link="";
			$url = "#";
		}
		$customData ['pagination'] .= "<li class=''><a href='$url' class='next $class_link'>&raquo;</a></li>";

		$customData['pagination'].='</ul>';
    	//return $paged['total_pages'];

     	if($pagination_always_visible || $paged['total_pages']>1 )			
    		return $customData['pagination'];				
        
    }
    
    // ==== Getters
/*     function get_current_page(){
    	$i=1;
    	while($this->uri->segment($i)){
    		if($this->uri->segment($i)=='page'){
    			$this->current_page=$this->uri->segment($i+1)?($this->uri->segment($i+1)):(1);
    			break;
    		}
    		$i++;
    	}
    	return $this->current_page;
    } */
    

    
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */