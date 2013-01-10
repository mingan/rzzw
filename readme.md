# Olympijský meshup

Tato aplikace zobrazuje informace o moderních olympijských hárch poskládané z několika zdrojů.

## Instalace

Pro instalaci stačí zkopírovat aplikaci do složky přístupné webovému serveru s těmito podmínkami:

- PHP 5.3 a vyšší pro běh serverové části
- cURL rozšíření pro PHP
- zapisovatelný adresář `/cached`

## Použité knihovny

- jQuery
- Bootstrap
- Google Maps JS API

## Zdroje dat
Všechna data pochází z níže uvedených zdrojůpři jejich výpadku aplikace nefunguje, pokud daný dotaz není uložen v cache.

### Freebase
Z Freebase se načítají horní tři položky a informace o medailistech pomocí MQL rozhraní.

### Google Maps Geocoding Service
Standardní geokódování přes API, zobrazují se pouze výsledky typu `country`.

### DBPedia
Přes DBPedia SPARQL endpoint se načítají medialonky sportovců

### NYTimes
Dvoukrokově se načítají novinové články o sportovcích. První krok dereferencuje RDF zdroj z NYT. Druhý krok volá NYT Search API.

### flickr wrappr
RDF wrappr nad flickr API poskytuje fotky na téma dané URI DBPedia zdroje. Výsledky vrací v XML.