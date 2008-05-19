<?php

class eZURLHandler extends BaseHandler {

	function exportAttribute( &$attribute )
	{
		$tempstring=$attribute->content() . ' ' . $attribute->DataText;
		return $this->escape( $tempstring );
	}
}

?>