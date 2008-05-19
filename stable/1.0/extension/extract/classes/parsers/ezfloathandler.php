<?php

class eZFloatHandler extends BaseHandler {

	function exportAttribute(&$attribute) {
		return $this->escape($attribute->content());
	}

}

?>