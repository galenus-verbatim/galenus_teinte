# teinte_galenus
Une version de teinte dédiée à la conversion d’OCR Galien

# Install

## Prerequisites

* PHP for command line
* [composer](https://getcomposer.org/download/), a package manager for PHP 

```bash
# clone GitHub repo
git clone https://github.com/galenus-verbatim/teinte_galenus.git
cd teinte_galenus
# install last version of required packages
composer u

```

# Usage 

```bash
php docx_tei.php examples/*.docx

info 0.013s. +0.013s. — Docx > tei, user xml template:
    file:///C:/code/teinte_galenus/galenus_tmpl.xml
info 0.014s. +0.001s. — Docx > tei, user regex loading:
    C:\code\teinte_galenus/galenus_pcre.tsv
info 0.018s. +0.004s. — examples\tlg0057.tlg001.1st1K-lat1.docx > 
    C:\code\teinte_galenus/out/tlg0057.tlg001.1st1K-lat1.xml

```