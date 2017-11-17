# EBPlantUMLBundle

This is a POC bundle for PlantUML : http://plantuml.sourceforge.net/.

## Format support

TXT files can be generated without PlantUML dependencies installed locally. If needed, you can use http://www.plantuml.com/plantuml/uml/ to render the PNG image from the exported TXT file.

Direct conversion to PNG files is only supported if all PlantUML requirements are met:

- Java JDK : `sudo apt install -y default-jdk`
- DOT Graphiz : `sudo apt install -y graphviz`

## Generate Doctrine UML graph

```bash
# TXT file
php app/console eb:uml:doctrine "/my/document/doctrine.txt"

# TXT file output to STDOUT
php app/console eb:uml:doctrine

# TXT file output to file
php app/console eb:uml:doctrine > doctrine.txt

# PNG file
php app/console eb:uml:doctrine "/my/document/doctrine.png"

# PNG file output to file
php app/console eb:uml:doctrine --format=png > doctrine.png
```

## Generate Twig inheritance tree

Use `-i` to include templates having their path matching your expression:

- `-i ProjectBundle`: include every paths containing "ProjectBundle"
- `-i app/Resources/views`: include every paths containing "app/Resources/views"

Use `-e` to exclude templates having their path matching your expression:

- `-e ExcludedBundle`: exclude every paths containing "ExcludedBundle"

```bash
# TXT file
php app/console eb:uml:twig "/my/document/twig.txt" -i AppBundle

# TXT file output to STDOUT
php app/console eb:uml:twig -i AppBundle

# TXT file output to file
php app/console eb:uml:twig -i AppBundle > twig.txt

# PNG file
php app/console eb:uml:twig "/my/document/twig.png" -i AppBundle

# PNG file output to file
php app/console eb:uml:twig -i AppBundle --format=png > twig.png
```

## Generate validation chart

```bash
# TXT file
php app/console eb:uml:validator "/my/document/validator.txt"

# TXT file output to STDOUT
php app/console eb:uml:validator

# TXT file output to file
php app/console eb:uml:validator > validator.txt

# PNG file
php app/console eb:uml:validator "/my/document/validator.png"

# PNG file output to file
php app/console eb:uml:validator --format=png > validator.png
```
