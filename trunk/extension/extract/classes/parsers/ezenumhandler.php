<?php
class eZEnumHandler extends BaseHandler
{
       function exportAttribute( &$attribute )
       {
            return $this->escape( $attribute->metaData() );
       }
}
?>