<?php

class hmregexplineHandler extends BaseHandler
{
	public function exportAttribute( &$attribute )
	{
		return $this->escape( utf8_decode($attribute->content()) );
	}
}

?>
