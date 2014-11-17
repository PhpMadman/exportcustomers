PrestaShop-MadModules
=====================

A simple module that export customers with address

ToDo for 2.0
=====================
```
1.5 and 1.6 compatible
Move $toExport to switched and text box
Automerge fields with same name
Sort box. If c.firstname and c.lastname has same name (Fullname) and lastname has pos 1, then it should be merged as Lastname Firstname
Delimiter to GUI
Merge seperator default space
Option to replace Gender Id with text.
id.dat should be moved to mysql
ps_export_fields
	field,active,sort,name
	a.id,1,1,ID
Options to enable utf8/iso csv.
	If both disable, auto enable utf8 without error.
Check what happens if a customer has 2 addresses.
Rewrite export function to a better code
Support more customer tabels. such as connections, countr_lang 
```


Changelog
=====================
```
Version 2.0
[-] Fixed module not beeing placed under Export tab in BO
[*] Moved export code to it's own function
```
