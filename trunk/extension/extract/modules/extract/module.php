<?php

$Module = array('name' => 'Extract');

$ViewList = array();
$ViewList['csv'] = array( 'script' => 'csv.php',
                          'functions' => array( 'csv' ),
            			  'default_navigation_part' => 'ezextractnavigationpart',
            			  'post_actions' => array( 'Download', 'BrowseSubtree', 'AddAttribute', 'Remove', 'RemoveData' ),
            			  'params' => array() );

$FunctionList = array();
$FunctionList['csv'] = array();

?>