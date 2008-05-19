<?php
/* Grapped code from http://ez.no/community/contribs/import_export/csv_extract_pubsvn
   , but the code doesn`t look fine.
   - Where the hell is eZStringUtils?
   - Why is a double return? 
*/

class eZMatrixHandler extends BaseHandler
{
    function exportAttribute(&$attribute)
    {
        $content = $attribute->content();
        $rows = $content->attribute( 'rows' );
        foreach( $rows['sequential'] as $row )
        {
            $matrixArray[] = eZStringUtils::implodeStr( $row['columns'], '_' );
        }
        return eZStringUtils::implodeStr( $matrixArray, '_' );
        return $this->escape( $matrixArray );
    }
}
?>