# EBPlantUMLBundle

This is a POC bundle for PlantUML : http://plantuml.sourceforge.net/.

## Format support

TXT files or URLs can be generated without PlantUML dependencies installed locally.

If needed, you can also use http://www.plantuml.com/plantuml/uml/ to render the PNG image from the TXT file.

Direct conversion to PNG/SVG/ATXT/UTXT files is only supported if all PlantUML requirements are met:

- Some Java JDK (for example `sudo apt install -y default-jdk`)
- DOT Graphiz (for example `sudo apt install -y graphviz`)

## Generate Doctrine UML graph

```bash
# TXT file
php app/console eb:uml:doctrine doctrine.txt
php app/console eb:uml:doctrine --format=txt > doctrine.txt

# PNG file
php app/console eb:uml:doctrine doctrine.png
php app/console eb:uml:doctrine --format=png > doctrine.png

# SVG file
php app/console eb:uml:doctrine doctrine.svg
php app/console eb:uml:doctrine --format=svg > doctrine.svg

# ASCII files (atxt or utxt)
php app/console eb:uml:doctrine doctrine.atxt
php app/console eb:uml:doctrine --format=atxt > doctrine.atxt
php app/console eb:uml:doctrine doctrine.utxt
php app/console eb:uml:doctrine --format=utxt > doctrine.utxt

# UML
php app/console eb:uml:doctrine --format=uml
```

## Generate Twig inheritance tree

Use `-i` to include templates having their path matching your expression:

- `-i ProjectBundle`: include every paths containing "ProjectBundle"
- `-i app/Resources/views`: include every paths containing "app/Resources/views"

Use `-e` to exclude templates having their path matching your expression:

- `-e ExcludedBundle`: exclude every paths containing "ExcludedBundle"

```bash
# TXT file
php app/console eb:uml:twig twig.txt -i AppBundle
php app/console eb:uml:twig -i AppBundle > twig.txt

# PNG file
php app/console eb:uml:twig twig.png -i AppBundle
php app/console eb:uml:twig -i AppBundle --format=png > twig.png

# SVG file
php app/console eb:uml:twig twig.svg -i AppBundle
php app/console eb:uml:twig -i AppBundle --format=svg > twig.svg

# ASCII files (atxt or utxt)
php app/console eb:uml:twig twig.atxt -i AppBundle
php app/console eb:uml:twig -i AppBundle --format=atxt > twig.atxt
php app/console eb:uml:twig twig.utxt -i AppBundle
php app/console eb:uml:twig -i AppBundle --format=utxt > twig.utxt

# URL
php app/console eb:uml:twig -i AppBundle --format=uml
```

## Generate validation chart

```bash
# TXT file
php app/console eb:uml:validator validator.txt
php app/console eb:uml:validator > validator.txt

# PNG file
php app/console eb:uml:validator validator.png
php app/console eb:uml:validator --format=png > validator.png

# SVG file
php app/console eb:uml:validator validator.svg
php app/console eb:uml:validator --format=svg > validator.svg

# ASCII files (atxt or utxt)
php app/console eb:uml:validator validator.atxt
php app/console eb:uml:validator --format=atxt > validator.atxt
php app/console eb:uml:validator validator.utxt
php app/console eb:uml:validator --format=utxt > validator.utxt

# URL
php app/console eb:uml:validator --format=uml
```
