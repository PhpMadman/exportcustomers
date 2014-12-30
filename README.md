MadModules
=====================

ExportCustomers
=====================

A simple module that export customers with address

ToDo for 2.0
=====================
```
1.5 and 1.6 compatible - 1.5 only compatible by using radio instead of switch
Option to replace Gender Id with text.
Option to replace Country id with text
	Manufacturer, State, Warehouse, pretty much everything with id_*
Options to export only active address / customer - Two diffrent switches
Support more customer tabels. such as connections, country_lang 
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
[+] Added upgrade code to auto-install database
[+] Automerge of fields with same name, disregards position
[+] Added option to set encoding
[+] Added code to replace new lines with / so csv won't break - thanks rufovi
[+] Added option only export customers / guests / both
[+] Added option to only export with newsletter
[+] Added option for gender text
```
