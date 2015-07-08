# MadModules
# ExportCustomers

A simple module that export customers with address

## ToDo for future releases
```
Add code to allow adding / editing / removing special fields
	This might even require support for php files, using all kinds of hooks system to add it in correct place
Option to replace * id with text
	Manufacturer, State, Warehouse, pretty much everything with id_*
```

## Changelog
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
[+] Added option to replace country id with name
[+] Added option to only export active customers
[+] Added logo

Version 2.0.1
[-] Fixed mysql error on install
[-] Fixed missing $this on _setSwitchType

Version 2.0.2
[-] Fixed undefined $input variable
[-] Tabs don't work on 1.5. Added a Scroll And Die interface
```
