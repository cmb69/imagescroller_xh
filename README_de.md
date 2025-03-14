# Imagescroller_XH

Imagescroller_XH ermöglicht die Anzeige einer scrollenden Slideshow von
Bildern (optional mit Links und einer Beschreibung). Es bestitzt keine
Back-End-Funktionalität um die Galerien zu verwalten. Statt dessen müssen
die Bilder per FTP oder mit dem Filebrowser von CMSimple_XH hoch geladen werden.
Zusätzliche Informationen für die Links und die Beschreibung muss manuell in
einer speziellen Textdatei eingegeben werden.

- [Voraussetzungen](#voraussetzungen)
- [Download](#download)
- [Installation](#installation)
- [Einstellungen](#einstellungen)
- [Verwendung](#verwendung)
- [Problembehebung](#problembehebung)
- [Lizenz](#lizenz)
- [Danksagung](#danksagung)

## Voraussetzungen

Imagescroller_XH ist ein Plugin für [CMSimple_XH](https://cmsimple-xh.org/de/).
Es benötigt CMSimple_XH ≥ 1.7.0, und PHP ≥ 7.1.0 mit der JSON Extension.
Imagescroller_XH benötigt weiterhin das [Plib_XH](https://github.com/cmb69/plib_xh) Plugin;
ist dieses noch nicht installiert (see *Einstellungen*→*Info*),
laden Sie das [aktuelle Release](https://github.com/cmb69/plib_xh/releases/latest)
herunter, und installieren Sie es.

## Download

Das [aktuelle Release](https://github.com/cmb69/imagescroller_xh/releases/latest)
kann von Github herunter geladen werden.

## Installation

Die Installation erfolgt wie bei vielen anderen CMSimple_XH-Plugins auch. Im
[CMSimple_XH Wiki](https://wiki.cmsimple-xh.org/de/?fuer-anwender/arbeiten-mit-dem-cms/plugins)
finden sie ausführliche Hinweise.

1. **Sichern Sie die Daten auf Ihrem Server.**
1. Entpacken Sie die ZIP-Datei auf Ihrem Computer.
1. Laden Sie den gesamten Order `imagescroller/` auf Ihren Server in den
   `plugins/` Ordner von CMSimple_XH hoch.
1. Vergeben Sie Schreibrechte für die Unterordner `config/`, `css/` und `languages/`.
1. Navigieren Sie zu `Plugins` → `Imagescroller`, und prüfen Sie, ob
    alle Voraussetzungen für den einwandfreien Betrieb erfüllt sind.

## Einstellungen

Die Konfiguration des Plugins erfolgt wie bei vielen anderen
CMSimple_XH Plugins auch im Administrationsbereich der Website. Navigieren
Sie zu `Plugins` → `Imagescroller`.

Sie können die Original-Einstellungen von Imagescroller_XH unter
`Konfiguration` ändern. Beim Überfahren der Hilfe-Icons mit der Maus
werden Hinweise zu den Einstellungen angezeigt.

Die Lokalisierung wird unter `Sprache` vorgenommen. Sie können die
Zeichenketten in Ihre eigene Sprache übersetzen, falls keine entsprechende
Sprachdatei zur Verfügung steht, oder sie entsprechend Ihren Anforderungen
anpassen.

Das Aussehen von Imagescroller_XH kann unter `Stylesheet` angepasst werden.

## Verwendung

Um einen Imagescroller mit allen Bildern des Ordners `userfiles/images/meine_galerie/`
anzuzeigen, fügen Sie auf einer Seite ein:

    {{{imagescroller('meine_galerie')}}}

Um den Imagescroller auf allen Seiten anzuzeigen, müssen sie die
`autoload` Konfigurationsoption aktivieren, und im Template einfügen:

    <?=imagescroller('meine_galerie')?>

Wenn Sie die Bilder verlinken möchten, oder Titel und Beschreibungen
hinzufügen möchten, müssen Sie eine Info-Datei anlegen. Diese muss im Ordner
`content/imagescroller/` abgelegt werden, und kann einen beliebigen Namen haben,
wobei die Dateierweiterung `.txt` lauten muss.
Um diese Datei zu bearbeiten, können Sie einen beliebigen Texteditor
verwenden, aber es ist wichtig, dass die Datei UTF-8 kodiert ist.

Die Datei enthält einen Datensatz für jedes Bild, das angezeigt werden soll;
die Datensätze werden durch eine Zeile, die nur zwei Prozentzeichen (`%%`)
enthält, getrennt. Die Datensätze können folgende Felder enthalten:
`Image` (Bild), `URL`, `Title` (Titel) und `Description` (Beschreibung);
nur `Image` ist erfoderlich, die anderen Felder sind optional.
Die Felder des Datensatzes werden in eigene Zeilen geschrieben,
die mit dem Feldnamen beginnen, gefolgt von einem Doppelpunkt und dem Wert des Feldes.
Die Reihenfolge der Zeilen spielt keine Rolle.
Der Dateiname des Bildes muss relativ zu `userfiles/images/` angegeben werden;
die URL kann relativ zur CMSimple_XH Site oder absolut sein.
Eine Beispieldatei sieht wie folgt aus: 

    Image: bild1.jpg
    URL: http://www.example.com/
    Title: Erstes Foto
    Description: Dies ist das erste Foto für den Imagescroller.
    %%
    Image: bild37.jpg
    URL: ?Eine_CMSimple_Seite
    %%
    Image: bild2.jpg
    URL: ?&amp;mailform
    Title: Kontakt
    %%
    Image: bild3.jpg
    URL: http://3-magi.net/de/
    Description: Meine Lieblings-Homepage ;)

Um den Imagescroller anzuzeigen, muss nur `imagescroller()` mit dem Namen der
Info-Datei (ohne die Dateierweiterung) aufgerufen werden, z.B.

    {{{imagescroller('info')}}}

Es ist zu beachten, dass alle Bilder die selbe Größe (d.h. Abmessungen)
haben sollten. Andernfalls werden sie auf die Größe des ersten Bildes der
Gallerie skaliert, und im Administrationsbereich wird eine Warnung
angezeigt.

## Problembehebung

Melden Sie Programmfehler und stellen Sie Supportanfragen entweder auf
[Github](https://github.com/cmb69/imagescroller_xh/issues)
oder im [CMSimple_XH Forum](https://cmsimpleforum.com/).

## Lizenz

Imagescroller_XH ist freie Software. Sie können es unter den Bedingungen
der GNU General Public License, wie von der Free Software Foundation
veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß
Version 3 der Lizenz oder (nach Ihrer Option) jeder späteren Version.

Die Veröffentlichung von Imagescroller_XH erfolgt in der Hoffnung, dass es
Ihnen von Nutzen sein wird, aber *ohne irgendeine Garantie*, sogar ohne
die implizite Garantie der *Marktreife* oder der *Verwendbarkeit für einen
bestimmten Zweck*. Details finden Sie in der GNU General Public License.

Sie sollten ein Exemplar der GNU General Public License zusammen mit
Imagescroller_XH erhalten haben. Falls nicht, siehe <https://www.gnu.org/licenses/>.

Copyright © 2012-2017 Christoph M. Becker

Slowakische Übersetzung © 2012 Dr. Martin Sereday  
Estnische Übersetzung © 2012 Alo Tänavots

## Danksagung

Imagescroller_XH verwendet [jQuery.serialScroll](https://github.com/flesler/jquery.serialScroll).
Vielen Dank an Ariel Flesler für die Veröffentlichung dieses netten jQuery-Plugins unter MIT Lizenz.

Das Pluginlogo wurde von [Everaldo Coelho](https://www.everaldo.com/) gestaltet.
Vielen Dank für die Veröffentlichung dieses Icons unter GPL.

Dieses Plugin verwendet Oxygen-Icons des [Oxygen-Themes](https://github.com/KDE/oxygen-icons).
Vielen Dank für die Veröffentlichung dieser Icons unter LGPLv3.

Vielen Dank an die Community im [CMSimple_XH Forum](http://www.cmsimpleforum.com/)
für Tipps, Anregungen und das Testen.

Und zu guter letzt vielen Dank an [Peter Harteg](http://www.harteg.dk/),
den „Vater“ von CMSimple, und allen Entwicklern von
[CMSimple_XH](https://www.cmsimple-xh.org/de/) ohne die es dieses
phantastische CMS nicht gäbe.
