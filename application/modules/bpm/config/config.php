<?php
$config['show_warn']	= false;
//---run mode development /  production
$config['run_mode']	= 'development';
//----Browser
$config['browser_tree_expanded']=false;
$config['tree_icon_model']='';
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
$config['auto_assign_admin']=true;
// Choose whether to make thumbnails or not
$config['make_thumbnails']=false;
// Choose whether to make backups or not
$config['make_model_backup']=true;
// File path
$config['bpm_file_path']="application/modules/bpm/assets/files/";
//---Task time parameters
//---Days before change
$config['task_ok']=15;
$config['task_warn']=30;
$config['task_danger']=60;
