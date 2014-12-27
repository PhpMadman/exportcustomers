MadModules
=====================

ExportCustomers
=====================

A simple module that export customers with address

ToDo for 2.0
=====================
```
1.5 and 1.6 compatible - 1.5 only compatible by using radio instead of switch
Merge seperator default space
Option to replace Gender Id with text.
Option to replace Country id with text
	Manufacturer, State, Warehouse, pretty much everything with id_*
Options to export only active address / customer
option to export guest / custome / both
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
[~] Removed posibilty to export address id_customer
[+] Added upgrade code
[+] Automerge of fields with same name, disregards position
[+] Added option to set encoding
[+] Added code to replace new lines with / so csv won't break - thanks rufovi
```
