<?php
$config['show_warn']	= false;
//---run mode development /  production
$config['run_mode']	= 'development';
//----Browser
$config['browser_tree_expanded']=false;
$config['tree_icon_model']='icon-ok-circle';
$config['browser_tree_checkable_folders']=false;
$config['browser_tree_checkable_models']=false;
//----Engine
//---auto create user groups based on lane names
$config['auto_create_groups']=true;
/*---allow administrators to run the tasks even if they don't belong to group
options for admins:
 */
// add me to the array of assigned  
$config['auto_add_admin']=false;
// replace the assignement with my id
$config['auto_assign_admin']=false;
// Choose whether to make thumbnails or not
$config['make_thumbnails']=false;