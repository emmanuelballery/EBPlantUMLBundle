EBPlantUMLBundle
================

This is a POC bundle.

# Requirements

PlantUML is used :
  - Java JDK is required (sudo apt-get install default-jdk)
  - DOT Graphiz is required (sudo apt-get install graphviz)

# Generate UML graph using doctrine entities

```bash
# Generate doctrine.png going threw all doctrine entities (using its metadata factory)
php app/console eb:uml:doctrine /var/www/Project/doctrine.png
```

# Generate Twig inheritance tree

```bash
# Generate twig.png
# Include all twig files with "ProjectBundle" and "app/Resources/views" on their names
# Exclude all files with "ExcludedBundle" on their names
php app/console eb:uml:twig /var/www/Project/twig.png -i ProjectBundle -i app/Resources/views -e ExcludedBundle
```
