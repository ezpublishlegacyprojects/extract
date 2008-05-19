<?php
class eZExampleHandler extends BaseHandler
{
	function exportAttribute( &$attribute )
	{
		$content =& $attribute->content();
		return $this->escape( $content );
	}
}
?>
