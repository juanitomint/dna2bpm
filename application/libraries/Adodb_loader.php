<?php
if (!defined('BASEPATH'))
        exit('No direct script access allowed');

class Adodb_loader {

        function Adodb_loader($params = null)
        {
                // check if adodb already loaded
                if (!class_exists('ADONewConnection'))
                {
                        require_once(APPPATH . 'vendor/adodb5/adodb.inc' . EXT);
                }

                // database handler's name, defaults to 'adodb'
                $dbh = (isset($params['name'])) ? $params['name'] : 'adodb';

                // the db settings group from the database.php config
                $db_group = (isset($params['group'])) ? $params['group'] : '';

                $this->_init_adodb_library($dbh, $db_group);
        }

        function _init_adodb_library($dbh, $db_group)
        {
                // get CI instance
                $CI = & get_instance();

                // get database config
                include(APPPATH . 'config/database' . EXT);

                // check which database group settings to use
                // default to database setting default
                $db_group = (!empty($db_group)) ? $db_group : $active_group;
                $cfg = $db[$db_group];

                // check that driver is set
                if (isset($cfg['dbdriver']))
                {
                        $CI->$dbh = & ADONewConnection($cfg['dbdriver']);

                        // set debug
                        $CI->$dbh->debug = $cfg['db_debug'];

                        // check for persistent connection
                        if ($cfg['pconnect'])
                        {
                                // persistent
                                $CI->$dbh->PConnect($cfg['hostname'], $cfg['username'], $cfg['password'], $cfg['database']) or die("can't do it: " . $CI->$dbh->ErrorMsg());
                        } else
                        {
                                // normal
                                $CI->$dbh->Connect($cfg['hostname'], $cfg['username'], $cfg['password'], $cfg['database']) or die("can't do it: " . $CI->$dbh->ErrorMsg());
                        }

                        // use associated array as default format
                        $CI->$dbh->SetFetchMode(ADODB_FETCH_ASSOC);
                } else
                {
                        die("database settings not set");
                }
        }

}