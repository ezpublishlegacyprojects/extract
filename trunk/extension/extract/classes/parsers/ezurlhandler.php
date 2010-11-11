<?php

class eZURLHandler extends BaseHandler
{
	public function exportAttribute( &$attribute )
	{
		$csvINI = eZINI::instance( 'csv.ini' );
		$ini_option = $csvINI->variable( 'General', 'StripURLText' );
		if ($ini_option == "true")
		{
			$tempstring = $attribute->content();
		}
		else
		{
			$tempstring = $attribute->content() . ' ' . $attribute->DataText;
		}
		return $this->escape( $tempstring );
	}
}

?>