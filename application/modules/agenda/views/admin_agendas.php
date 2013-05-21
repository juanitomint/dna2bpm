<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <!-- Use the .htaccess and remove these lines to avoid edge case issues.
       More info: h5bp.com/i/378 -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title>Agenda</title>
  <meta name="description" content="">
<!-- First CSS-->
<link rel="stylesheet" href="{module_url}assets/css/style_first.css">
<link rel="STYLESHEET" type="text/css" href="{module_url}assets/css/main.css" media="screen" />  
<link rel="STYLESHEET" type="text/css" href="{module_url}assets/css/admin.css" media="screen" /> 

<!-- Last CSS -->
<link rel="stylesheet" href="{module_url}assets/css/style_last.css">
  <!-- Mobile viewport optimized: h5bp.com/viewport -->
  <meta name="viewport" content="width=device-width">
  


</head>
<body >
<div style="overflow:scroll;height:100%;width:auto;position:relative;padding:10px">
{agendas}



</div>
    <!-- Jquery -->
<script src="{module_url}assets/jscript/libs/jquery-1.7.2.min.js"></script>
<script src="{module_url}assets/jscript/libs/jquery-ui-1.9.2.custom.min.js"></script>
<script src="{module_url}assets/jscript/libs/jquery.mjs.nestedSortable.js"></script>
<script type="text/javascript">              
                   
 // JQUERY ONLOAD //
$(document).ready(function(){

		$('ol.sortable').nestedSortable({
			forcePlaceholderSize: true,
			handle: 'div',
			helper:	'clone',
			items: 'li',
			opacity: .6,
			placeholder: 'placeholder',
			revert: 250,
			tabSize: 25,
			tolerance: 'pointer',
			toleranceElement: '> div',
			maxLevels: 7,
			isTree: true,
			expandOnHover: 700,
			startCollapsed: true
		});


});
    
     
  </script>
</body>
</html>