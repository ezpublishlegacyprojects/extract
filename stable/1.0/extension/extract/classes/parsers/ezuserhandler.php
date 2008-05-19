<?

class eZUserHandler extends BaseHandler
{
	function exportAttribute(&$attribute ) {
		$content =& $attribute->content();
		return $this->escape( $content->attribute( 'login' ) );
	}
}
?>
