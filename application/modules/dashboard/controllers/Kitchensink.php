<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * test
 * 
 * Description of the class
 * 
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date    May 28, 2014
 */
class Kitchensink extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('user/user');
        $this->load->library('dashboard/ui');

    
    }

    function Index() {
          Modules::run('dashboard/dashboard', 'dashboard/json/demo.json');
    }
    
//== Callouts

    function callout(){
        $config=array('body'=>'Im a callout','title'=>'Callout','class'=>'info');
        echo <<<'_EOF_'
        <code><br>$this->load->library('dashboard/ui');<br>
        $config=array('body'=>'Esto es un callout','title'=>'Callout','class'=>'info');<br>
        echo $this->ui->callout($config);<br>
        </code>
_EOF_;

        echo $this->ui->callout($config);
    }
    
//== Alerts

    function alert(){
        
        echo <<<'_EOF_'
        <code>$this->load->library('dashboard/ui');<br>
        $config=array('body'=>'This is an alert!','class'=>'info','dismissable'=>true,'icon'=>true,'icon_class'=>'fa-info');<br>
        echo $this->ui->alert($config);
        </code><p style="height:15px"></p>
_EOF_;
        $config=array('body'=>'This is a custom alert!','class'=>'info','dismissable'=>true,'icon'=>true,'icon_class'=>'fa-info');
        echo $this->ui->alert($config);
        
        echo <<<'_EOF_'
        
        <h4>Alert wrappers</h4>
        <code>
         $this->ui->alert_info("This is an info alert");<br>
         $this->ui->alert_danger("This is a danger alert");<br>
         $this->ui->alert_warning("This is a warning alert");<br>
         $this->ui->alert_success("This is a success alert");<br>
        </code><p style="height:15px"></p>
_EOF_;

        echo $this->ui->alert_info("This is an info alert");
        echo $this->ui->alert_danger("This is a danger alert");
        echo $this->ui->alert_warning("This is a warning alert");
        echo $this->ui->alert_success("This is a success alert");
        
    }
  
//=== Proress

    function progress(){
        echo <<<'_EOF_'
        <code>$this->load->library('dashboard/ui');<br>
         $config=array('width'=>'50','class'=>'info','active'=>true,'stripped'=>true,'size'=>'md');<br>
         //size:xs,sm,md<br>
        echo $this->ui->progress($config);
        </code><p style="height:15px"></p>
_EOF_;
        $config=array('width'=>'50','class'=>'info','active'=>true,'stripped'=>true,'size'=>'');
        echo $this->ui->progress($config);
        
        // wrappers
        
    echo <<<'_EOF_'
    <h4>Progress wrappers</h4>
        <code>
        echo $this->ui->progress_info(50);<br>
        echo $this->ui->progress_danger(50);<br>
        echo $this->ui->progress_warning(50);<br>
        echo $this->ui->progress_success(50);<br>
        </code><p style="height:15px"></p>
_EOF_;

        echo $this->ui->progress_info(50);
        echo $this->ui->progress_danger(50);
        echo $this->ui->progress_warning(50);
        echo $this->ui->progress_success(50);
    }
 
//=== Lists

      function lists(){
        
        echo <<<'_EOF_'
        <code>$data=array('item1','item2',array('item3a','item3b',array('item4')));<br>
        echo $this->ui->ol($data);<br>
        echo $this->ui->ul($data);<br>
        </code><p style="height:15px"></p>
_EOF_;

        $data=array('item1','item2',array('item3a','item3b',array('item4')));
        echo $this->ui->ol($data);
        echo $this->ui->ul($data);
        
        echo <<<'_EOF_'
        <h4>Unstyled lists</h4>
        <code>
        echo $this->ui->ol($data,true);<br>
        echo $this->ui->ul($data,true);<br>
        </code><p style="height:15px"></p>
_EOF_;
        echo $this->ui->ol($data,true);
        echo $this->ui->ul($data,true);
    }  
    

function boxes(){
    
echo <<<_EOF_
<code>  
box-icon: <fontawesome class><br>
box-class: [box [(btn-primary|btn-danger|btn-info|btn-warning|box-success)] [box-solid]]<br>
box-buttons: [[remove] [collapse]]<br>
box-collapsed: [[(0|1)]]<br>
 </code><br>
i.e.:

    "box_icon":"fa-pie-chart",
    "box_class":["box"],
    "box_buttons":["remove","collapse"],
    "box_collapsed":"0"


_EOF_;
    
}
//=== Pagination

function pagination(){
    
$text=<<<_EOF_
Linea 1<br>
Linea 2<br>
<!-- pagebreak -->
Linea 3<br>
Linea 4<br>
<!-- pagebreak -->
Linea 5<br>
Linea 6<br>
<!-- pagebreak -->
Linea 7<br>
Linea 8<br>
<!-- pagebreak -->
Linea 9<br>
Linea 10<br>
<!-- pagebreak -->
Linea 11<br>
Linea 10<br>
<!-- pagebreak -->
Linea 12<br>
Linea 13<br>
_EOF_;

echo <<<'_EOF_'
<code>
$this->load->library('dashboard/ui');<br>
$config['width']=4 //optional<br>
$config['sep']='&lt;!-- pagebreak -->' //optional<br>
$config['align']='right'//optional (left|center|right)
/* If $text is a string, the pages will be extracted using the separator. If it is an array, each array item is a page and must have strings inside.*/<br>

echo $this->ui->paginate($text,$config);
</code><p style="height:15px"></p>
_EOF_;
    

echo $this->ui->paginate($text,array('width'=>4));


  }

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */