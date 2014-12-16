PrestaShop-MadModules
=====================

A simple module that export customers with address

ToDo for 2.0
=====================
```
1.5 and 1.6 compatible
Move $toExport to switched and text box
	Re think that, for full sport, we would need, on/off,name,position in csv.
	We could do a tabbed interface. 1 general, 2 activate fields, 3 sort active fileds ?
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
Select  for export customers with newsleter / no newsletter / both
a.other can have r\n, use a  str_replace to replaced it, based on idea from rufovi
```


Changelog
=====================
```
Version 2.0
[-] Fixed module not beeing placed under Export tab in BO
[*] Moved export code to it's own function
```
