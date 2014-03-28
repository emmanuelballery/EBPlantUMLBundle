EBPlantUMLBundle
================

This is a POC bundle for PlantUML : http://plantuml.sourceforge.net/.

## Requirements

PlantUML require :
  - Java JDK (sudo apt-get install default-jdk)
  - DOT Graphiz (sudo apt-get install graphviz)

## Generate UML graph using doctrine entities

To generate an image "/var/www/Project/doctrine.png" using all doctrine entities (referenced by the metadata factory).

```bash
php app/console eb:uml:doctrine /var/www/Project/doctrine.png
```

## Generate Twig inheritance tree

To generate an image "/var/www/Project/twig.png" including all twig templates with "ProjectBundle" and
"app/Resources/views" in their filenames and excluding all files with "ExcludedBundle" in their filenames.

```bash
php app/console eb:uml:twig /var/www/Project/twig.png -i ProjectBundle -i app/Resources/views -e ExcludedBundle
```
