[General]
ExportableDatatypes[]
ExportableDatatypes[]=ezboolean
ExportableDatatypes[]=eztext
ExportableDatatypes[]=ezinteger
ExportableDatatypes[]=ezstring
#ExportableDatatypes[]=eztime
ExportableDatatypes[]=ezurl
ExportableDatatypes[]=ezuser
ExportableDatatypes[]=ezxmltext
#ExportableDatatypes[]=ezboolean
ExportableDatatypes[]=ezdate
#ExportableDatatypes[]=ezdatetime
ExportableDatatypes[]=ezemail
ExportableDatatypes[]=ezfloat
ExportableDatatypes[]=ezidentifier
ExportableDatatypes[]=ezenhancedobjectrelation
ExportableDatatypes[]=ezenhancedselection
ExportableDatatypes[]=ezselection
ExportableDatatypes[]=ezenum
ExportableDatatypes[]=ezcountry

#all handlerfiles are sought in the base directory (extension/csvexport/modules/csvexport/), if you want 
#to place them some place down the path from there, add the path from there.

[ezstring]
HandlerFile=ezstringhandler.php
HandlerClass=eZStringHandler

[ezenhancedselection]
HandlerFile=ezstringhandler.php
HandlerClass=eZStringHandler

[ezinteger]
HandlerFile=ezintegerhandler.php
HandlerClass=eZIntegerHandler

[ezxmltext]
HandlerFile=ezxmltexthandler.php
HandlerClass=eZXMLTextHandler

[ezidentifier]
HandlerFile=ezidentifierhandler.php
HandlerClass=eZIdentifierHandler

[ezfloat]
HandlerFile=ezfloathandler.php
HandlerClass=eZFloatHandler

[ezemail]
HandlerFile=ezemailhandler.php
HandlerClass=eZEmailHandler

[ezurl]
HandlerFile=ezurlhandler.php
HandlerClass=eZURLHandler

[eztext]
HandlerFile=eztexthandler.php
HandlerClass=eZTextHandler

[ezenhancedobjectrelation]
HandlerFile=ezenhancedobjectrelationhandler.php
HandlerClass=ezenhancedobjectrelationHandler
#false will simply output the IDs of the related objects
OutputRelatedObjectNames=true

[ezselection]
HandlerFile=ezselectionhandler.php
HandlerClass=eZSelectionHandler

[ezenum]
HandlerFile=ezenumhandler.php
HandlerClass=eZEnumHandler

[ezuser]
HandlerFile=ezuserhandler.php
HandlerClass=eZUserHandler

[ezdate]
HandlerFile=ezdatehandler.php
HandlerClass=eZDateHandler

[ezboolean]
HandlerFile=ezbooleanhandler.php
HandlerClass=eZBooleanHandler

[ezcountry]
HandlerFile=ezselectionhandler.php
HandlerClass=eZSelectionHandler