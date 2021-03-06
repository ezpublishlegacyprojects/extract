<?
include_once('lib/ezutils/classes/ezini.php');
include_once('extension/extract/classes/basehandler.php');

/*
return the datatypes (identifiers) that can be exported
*/

class ParserInterface
{

//holds the lookup map for the handler mapings
var $handlerMap=array();
var $exportableDatatypes;
var $separationChar = ",";
var $escape = true;
	function ParserInterface( $separationChar = null, $escape = null )
	{
	    if ( $escape === true or $escape === false )
	       $this->escape = $escape;
	    if ( $separationChar !== null )
	       $this->separationChar = $separationChar;
		$ini = eZINI::instance( "csv.ini" );
		$this->exportableDatatypes=$ini->variable( "General", "ExportableDatatypes" );
		foreach ($this->exportableDatatypes as $typename)
		{
		    if ( file_exists( "extension/extract/classes/parsers/".$ini->variable($typename, 'HandlerFile') ) )
		    {
			     include_once("extension/extract/classes/parsers/".$ini->variable($typename, 'HandlerFile'));
			     $classname=$ini->variable($typename, 'HandlerClass'); 
			     $handler= new $classname( $this->separationChar );
			     $handler->separationChar = $this->separationChar;
			     $handler->escape = $this->escape;
			     $this->handlerMap[$typename]=array("handler" => $handler,
						"exportable" => true);
		    
		    }
		    else
		      eZDebug::writeError( "Error loading " . $ini->variable($typename, 'HandlerFile'), "Extract");
		}
	}
	
	function getExportableDatatypes()
	{
		return $this->exportableDatatypes;
	}
	
	/*
		Export an attribute to a string
	*/

	function exportAttribute( &$attribute ) {
		$handler=$this->handlerMap[$attribute->DataTypeString]['handler'];
		if( is_object( $handler ) )
		{
		  return $handler->exportAttribute( $attribute ).$this->separationChar;
		}
		else
		  return $this->separationChar;
	}
}