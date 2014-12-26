MadModules
=====================

ExportCustomers
=====================

A simple module that export customers with address

ToDo for 2.0
=====================
```
1.5 and 1.6 compatible - 1.5 only compatible by using radio instead of switch
Automerge fields with same name
Sort box. If c.firstname and c.lastname has same name (Fullname) and lastname has pos 1, then it should be merged as Lastname Firstname
Merge seperator default space
Option to replace Gender Id with text.
Option to replace Country id with text
	Manufacturer, State, Warehouse, pretty much everything with id_*
Options to enable utf8/iso csv.
	If both disable, auto enable utf8 without error.
Options to export only active address / customer
option to export guest / custome / both
Check what happens if a customer has 2 addresses. - 2 rows?
Rewrite export function to a better code
Support more customer tabels. such as connections, country_lang 
Select  for export customers with newsleter / no newsletter / both
a.other can have r\n, use a  str_replace to replaced it, based on idea from rufovi
add support for reading a $toExport file and fix the mysql table
Create tags for module versions, make relleases. Zip is then named MadModules-exportcustomer-2.0
Add link to /releases in readme
```


Changelog
=====================
```
Version 2.0
[-] Fixed module not beeing placed under Export tab in BO
[*] Moved export code to it's own function
[+] Added tabbed GUI
[~] Removed posibilty to export address customer_id
[+] Added upgrade code
```
