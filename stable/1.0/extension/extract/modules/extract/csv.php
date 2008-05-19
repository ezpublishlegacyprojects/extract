<?php

// Include toolbox
include_once("kernel/common/template.php");
include_once('lib/ezutils/classes/ezhttptool.php');
include_once('kernel/content/ezcontentfunctioncollection.php');

// Array of extra node attributes
$ExtraAttributes = array(
			'ezuser.login' => array(
						'id' => 'ezuser.login', 
						'exportname' => 'login', 
						'name' => 'Login',
						'include' => 'kernel/classes/datatypes/ezuser/ezuser.php',
						'function' => 'fetch'), 
			'ezuser.email' => array(
						'id' => 'ezuser.email', 
						'exportname' => 'email', 
						'name' => 'E-Mail',
						'include' => 'kernel/classes/datatypes/ezuser/ezuser.php',
						'function' => 'fetch'), 
			'ezuser.password_hash' => array(
						'id' => 'ezuser.password_hash',
						'exportname' => 'password', 
						'name' => 'Password',
						'include' => 'kernel/classes/datatypes/ezuser/ezuser.php',
						'function' => 'fetch'),
			'ezcontentobject.published' => array(
						'id' => 'ezcontentobject.published',
						'exportname' => 'published', 
						'name' => 'Content Object Published Time',
						'filter' => 'date',
						'include' => 'kernel/classes/ezcontentobject.php',
						'function' => 'fetch'),
			'ezcontentobject.modified' => array(
						'id' => 'ezcontentobject.modified',
						'exportname' => 'modified', 
						'name' => 'Content Object Modified Time',
						'filter' => 'date',
						'include' => 'kernel/classes/ezcontentobject.php',
						'function' => 'fetch'),
			'ezcontentobject.main_parent_node_id' => array(
						'id' => 'ezcontentobject.main_parent_node_id',
						'exportname' => 'parent_name', 
						'name' => 'Content Object Main Parent Name',
						'filter' => 'parent_name',
						'include' => 'kernel/classes/ezcontentobject.php',
						'function' => 'fetch'),
			'ezcontentobject.parent_nodes' => array(
						'id' => 'ezcontentobject.parent_nodes',
						'exportname' => 'parent_nodes', 
						'name' => 'Content Object Parent Names',
						'filter' => 'parent_nodes',
						'include' => 'kernel/classes/ezcontentobject.php',
						'function' => 'fetch')
);

// Start module definition
$module =& $Params["Module"];

// Parse HTTP POST variables
$http =& eZHTTPTool::instance();
// Access system variables
$sys = eZSys::instance();
// Init template behaviors 
$tpl =& templateInit();
// Access ini variables
$ini =& eZINI::instance();
$ini_bis =& eZINI::instance('export.ini.append');

if ( isset( $_SESSION['EXTRACTCSV_OBJECTID_ARRAY'] ) and count( $_SESSION['EXTRACTCSV_OBJECTID_ARRAY'] ) > 0 )
    $hasPreFilledData = true;
else
    $hasPreFilledData = false;
    
if ( $hasPreFilledData and $http->hasPostVariable('RemoveData') )
{
    unset( $_SESSION['EXTRACTCSV_OBJECTID_ARRAY'] );
    
    return $module->redirectTo( 'extract/csv' );

}
$sessionConfig = eZHTTPTool::sessionVariable( 'eZExtractConfig' );
// Set col & row separator
$Separator = $http->hasPostVariable('Separator') ? $http->postVariable('Separator') : ',';

$LineSeparator = $http->hasPostVariable('LineSeparator') ? $http->postVariable('LineSeparator') : $sys->osType();

$LineSeparatorArray = array('win32' => array('id' => 'win32', 'value' => "\r\n", 'name' => 'Windows'),
			    'unix' => array('id' => 'unix', 'value' => "\n", 'name' => 'Unix'),
			    'mac' => array('id' => 'mac', 'value' => "\r", 'name' => 'Mac'),
);

$tpl->setVariable('Separator', $Separator);
$tpl->setVariable('LineSeparator', $LineSeparator);
$tpl->setVariable('LineSeparatorArray', $LineSeparatorArray);

// Set limit & offset
$Limit = $http->hasPostVariable('Limit') ? $http->postVariable('Limit') : $ini_bis->variable('ExportSettings','Limit');
$Offset = $http->hasPostVariable('Offset') ? $http->postVariable('Offset') : $ini_bis->variable('ExportSettings','Offset');

$tpl->setVariable('Limit', $Limit);
$tpl->setVariable('Offset', $Offset);

// What is the default subtree
if(!$http->hasPostVariable('Subtree'))
{
	$Subtree = ($ini_bis->variable('ExportSettings','StartNodeID') == '') ? $ini->variable('UserSettings', 'DefaultUserPlacement') : $ini_bis->variable('ExportSettings','StartNodeID');
}
else 
{
	$Subtree = $http->postVariable('Subtree');
}
// What is the default fetch type
if(!$http->hasPostVariable('type'))
{
	$type = 'tree';
}
else 
{
	$type = $http->postVariable('type');
}

if ( $type == 'list' )
    $depth['field'] = 1;
else
	$depth = false;

if( !$http->hasPostVariable('mainnodeonly') )
{
	$Mainnodeonly = '0';
}
else 
{
	$Mainnodeonly = $http->postVariable('mainnodeonly');
}

if( !$http->hasPostVariable('Escape') )
{
	$Escape = true;
}
else 
{
    if ( $http->postVariable('Escape') )
       $Escape = true;
    else
	   $Escape = false;
}

if ( !$hasPreFilledData )
{
    // What is the default class
    if(!$http->hasPostVariable('Class_id'))
    {
	   $Class_id = ($ini_bis->variable('ExportSettings','DefaultClassID') == '') ? $ini->variable('UserSettings', 'UserClassID') : $ini_bis->variable('ExportSettings','DefaultClassID');
    }
    else 
    {
	   $Class_id = $http->postVariable('Class_id');
    }
}
else
{
    $obj = eZContentObject::fetch( $_SESSION['EXTRACTCSV_OBJECTID_ARRAY'][0] );
    $Class_id = $obj->attribute( 'contentclass_id' );
}


if($http->hasPostVariable('SelectedNodeIDArray'))
{
	$nodes = $http->postVariable('SelectedNodeIDArray');
	$Subtree = $nodes[0];
}

// If we don't remove, add or download then or we load all attributes or we start empty
if($http->hasPostVariable('Remove') || $http->hasPostVariable('AddAttribute') || $http->hasPostVariable('Download'))
{
	$Attributes = $http->postVariable( 'Attributes' );
}
else
{
	if ( array_key_exists( 'Attributes', $sessionConfig ) and array_key_exists( $Class_id, $sessionConfig['Attributes'] )  )
	{
	    $Attributes = $sessionConfig['Attributes'][$Class_id]; 
	}
	else if($ini_bis->variable('ExportSettings','PreselectAttributes') == 'false')
	{
		$Attributes = array();
	}
	else
	{
		$contentAttributeList =& eZContentClassAttribute::fetchListByClassID($Class_id, EZ_CLASS_VERSION_STATUS_DEFINED, true );

		foreach($contentAttributeList as $classattribute )
		{
			$Attributes[] = array(   'id' => $classattribute->attribute('identifier'),
			                         'name' => $classattribute->attribute('name'), 'exportname' => $classattribute->attribute( 'identifier' ) );
		}
	}
}

// Add attribute action that modify previous array
if($http->hasPostVariable('AddAttribute'))
{
	$addID = $http->postVariable('AddAttributeID');

	if(is_numeric($addID))
	{
		$attribute = eZContentClassAttribute::fetch($addID);
		$element = array('id' => $attribute->attribute('identifier'), 'name' => $attribute->attribute('name'), 'exportname' => $attribute->attribute('identifier'));
		$Attributes[] = $element;
	}
	else
	{
		$Attributes[] = $ExtraAttributes[$addID];
	}
}

// Remove action that modify previous array
if($http->hasPostVariable('Remove') && $http->hasPostVariable('RemoveIDArray'))
{
	$AttributesClean = array();

	$Removes = $http->postVariable('RemoveIDArray');

	for($i=0; $i < count($Attributes); $i++)
	{
		if(!in_array($i, $Removes))
		    $AttributesClean[] = $Attributes[$i];
	}
	$Attributes = $AttributesClean;
}
$sessionConfig['Attributes'][$Class_id] = $Attributes;
eZHTTPTool::setSessionVariable( 'eZExtractConfig', $sessionConfig );
// Put above vars in tpl
$tpl->setVariable('Type', $type);
$tpl->setVariable('Subtree', $Subtree);
$tpl->setVariable('Class_id', $Class_id);
$tpl->setVariable('Attributes', $Attributes);
$tpl->setVariable('ExtraAttributes', $ExtraAttributes );
$tpl->setVariable('Mainnodeonly', $Mainnodeonly );
$tpl->setVariable('has_prefilledata', $hasPreFilledData );

/*
    function fetchObjectTreeCount( $parentNodeID, $onlyTranslated, $language, $class_filter_type, $class_filter_array,
                                   $attributeFilter, $depth, $depthOperator,
                                   $ignoreVisibility, $limitation, $mainNodeOnly, $extendedAttributeFilter, $objectNameFilter )
*/
$list =& eZContentFunctionCollection::fetchObjectTreeCount($Subtree, false, false, 'include', array($Class_id),
                                   false, false, false,
							      false, false, true, false, false );
$tpl->setVariable( 'max_count', $list['result'] + 1 );					      
// Handle download action
if($http->hasPostVariable('Download'))
{
	include_once('lib/ezfile/classes/ezfile.php');

	$dir =  eZSys::cacheDirectory().'/';
	$file = $dir.'export.csv';

	foreach($Attributes as $item)
	{
		$row .= $item['exportname'].$Separator;
	}

	$data = $row.$LineSeparatorArray[$LineSeparator]['value'];


    
if ( $hasPreFilledData )
{
    $list = $_SESSION['EXTRACTCSV_OBJECTID_ARRAY'];
}
else
{
    // Retrieve parent_node_id sort_array
	$node =& eZContentObjectTreeNode::fetch($Subtree);
	$sortBy =& $node->sortArray();
	$sortBy = $sortBy[0];

	/*
    function fetchObjectTree( $parentNodeID, $sortBy, $onlyTranslated, $language, $offset, $limit, $depth, $depthOperator,
                              $classID, $attribute_filter, $extended_attribute_filter, $class_filter_type, $class_filter_array,
                              $groupBy, $mainNodeOnly, $ignoreVisibility, $limitation, $asObject, $objectNameFilter )
	*/

	$list2 =& eZContentFunctionCollection::fetchObjectTree($Subtree, $sortBy, false, false, $Offset, $Limit, $depth, false,
							      $Class_id, false, false, 'include', array($Class_id),
							      $groupBy, false, true, true, true);
    $list =& $list2['result'];
}
	foreach( $list as $item )
	{
		$row = '';
		if ( is_object( $item ) )
            $obj =& $item->attribute('object');
        else
            $obj =& eZContentObject::fetch( $item );
        if ( !is_object( $obj ) )
            continue;
		$datamap =& $obj->attribute('data_map');
		include_once( 'extension/extract/classes/parserinterface.php' );
        $parser = new ParserInterface( $Separator, $Escape );
        
		foreach($Attributes as $dataelement)
		{
			$found = false;

			if( is_object( $datamap[$dataelement['id']] ) )
			{
			    $row .= $parser->exportAttribute( $datamap[ $dataelement['id'] ] );
			}
			else if(preg_match('#(.*)\.(.*)#', $dataelement['id'], $matches))
			{
				include_once($ExtraAttributes[$dataelement['id']]['include']);

				$id = $obj->attribute('id');
				$tmp = new $matches[1];
				$tmp = $tmp->$ExtraAttributes[$dataelement['id']]['function']($id);

				if(array_key_exists('filter', $ExtraAttributes[$dataelement['id']]))
				{
					$tmp = applyOutputFilter($tmp->attribute($matches[2]), $ExtraAttributes[$dataelement['id']]['filter']);
				}
                
				if(is_object($tmp))
					$row .=  BaseHandler::escape( $tmp->attribute( $matches[2] ) ) . $Separator;
				else
					$row .=  BaseHandler::escape( $tmp  ).  $Separator;

				unset($tmp);
			}
			else
			{
				$row .= $Separator;
			}
		}
		$data .= $row.$LineSeparatorArray[$LineSeparator]['value'];
	}

	@unlink($file);
	eZFile::create($file, false, $data);

	if(! eZFile::download($file)) $module->redirectTo('content/view/full/5');
}

if($http->hasPostVariable('BrowseSubtree'))
{
	include_once('kernel/classes/ezcontentbrowse.php');

	$return = eZContentBrowse::browse(array('action_name' => 'ExtractionSubtree',
						'description_template' => 'design:extract/browse_node.tpl',
						'from_page' => '/extract/csv',
						'persistent_data' => array('Subtree' => $Subtree,
									    'Class_id' => $Class_id,
									    'Attributes' => $Attributes,
									    'LineSeparator' => $LineSeparator,
									    'Separator' => $Separator)),
					 $module);
}

$Result = array();
$Result['content'] =& $tpl->fetch("design:extract/csv.tpl");
$Result['path'] = array(    array('url' => false,
			                      'text' => ezi18n('design/standard/extract', 'Extract') ),
			                array('url' => false,
			                      'text' => ezi18n('design/standard/extract', 'CSV') )
			                       );
// New variables of 3.8
$Result['left_menu'] = 'design:extract/menu.tpl';

function applyOutputFilter($tmp, $filtername)
{
	switch($filtername)
	{
		case "date":
			$tmp = strftime("%Y-%m-%d", $tmp);
		break;
		case "parent_name":
			$node =& eZContentObjectTreeNode::fetch($tmp);
			$tmp = $node->attribute('name');
		break;
		case "parent_nodes":
			$names = array();
			foreach ($tmp as $node_id)
			{
				$node =& eZContentObjectTreeNode::fetch($node_id);
				$names[] = $node->attribute('name');
			}
			$tmp = join(" ", $names);
		break;
		default:
		break;
	}
	return $tmp;
}

?>