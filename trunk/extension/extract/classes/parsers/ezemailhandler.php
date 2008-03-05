<?php

class eZEmailHandler extends BaseHandler {

	function exportAttribute( &$attribute ) {
		
		return $this->escape( $attribute->content() );
	}

}

?>