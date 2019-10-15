# modangci
Create, Read, Update and Delete (CRUD) Generator for Codeiginer3.

## Status
Unstable

## Installation
Create project Codeigniter
1. composer create-project kenjis/codeigniter-composer-installer codeigniter
2. cd codeigniter/
3. composer require agusth24/modangci master@dev
4. php vendor/agusth24/modangci/install
5. php modangci make controller example

## List of Command Make:
- Make controller: make controller controller_name [extends_name] [-r]
- Make model: make model model_name [extends_name] [table_name] [primary_key]
- Make helper: make helper helper_name
- Make libraries: make libraries libraries_name
- Make view: make view name_name
- Make view: make crud name

## List of Command Import:
- Import model master: import model master
- Import helper format date Indonesia: import helper datetoindo
- Import helper format hari Indonesia: import helper daystoindo
- Import helper format bulan Indonesia: import helper monthtoindo
- Import helper generate password number: import helper generatepassword
- Import helper logging crud: import helper debuglog
- Import helper terbilang: import helper terbilang
- Import helper message alert: import helper message
- Import libraries create pdf document: import libraries pdfgenerator
- Import libraries encryptions: import libraries encryptions

## List of Command Init:
- Scaffolding Authentication Login: init auth
- Scaffolding Controller: init controller table_name controller_class controller_display
- Scaffolding Model: init model table_name model_class
- Scaffolding View: init view table_name folder_name
- Scaffolding CRUD: init crud table_name class_name display_name
