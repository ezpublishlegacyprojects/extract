<?php
class eZDateHandler extends BaseHandler
{
       function exportAttribute( &$attribute )
       {
            return $this->escape( strftime( '%Y-%m-%d', $attribute->metaData() ) );
       }
}
?>