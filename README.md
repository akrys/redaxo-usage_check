[english](README_en.md)

# Usage Check

Dieses Addon sucht nach Bildern, Module und Templates, die nicht (mehr) verwendet werden.

## Installation

Das Repo beinhaltet direkt die Dateien für das Verzeichnis
`redaxo/inlcude/addons/usage_check` (Redaxo 4.x) oder
`redaxo/src/addons/usage_check` (Redaxo 5.x)

Das Verzeichnis selber (`usage_check`) ist hier nicht enthalten, da man das
Addon sonst nicht direkt an die richtige Stelle auschecken könnte.

Man kann auch das hier generierte ZIP herunterladen und an die richtige Stelle
kopieren.

Dann muss man das Addon nur noch über das Redaxo-Backend installieren und
aktivieren.

## Kompatibilität
- PHP Version: __5.3.2__ oder höher
- getestet mit Redaxo __4.3.2__, __4.4.1__, __4.5__, __4.6.1__, __4.7__, __5.0.1__, __5.1__, __5.2__

Der Code funktioniert für Redaxo 4 und Redaxo 5

##Hinweis für die Sprachdateien

Der Ordner `usage_check/lang` braucht Schreibrechte für den PHP bzw. Apache User.

Grund dafür ist, dass von mir nur die Dateien `de_de_utf8.lang` und
`en_gb_utf8.lang` gepflegt werden.

Jetzt wird es etwas undurchsichtig: Für Redaxo 4 braucht es den Inhalt der
Dateien `de_de_utf8.lang` und `en_gb_utf8.lang` in __ISO-8859-1__. Da diese
Dateien in Redaxo 5 aber __UTF-8__ kodiert sein müssen, muss ich diese Dateien
je nach verwendeter Redaxo-Version aus den `xx_yy_utf8.lang` Dateien in der
richtigen Zeichenkodierung erstellen. Dafür braucht es dann Schreibrechte am
Verzeichnis.

Leider kommt man da nicht drum herum, wenn der Code für beide Redaxo-Versionen
kompatibel sein soll und man gleichzeitig die Sprachdateien nur 1x pflegen will.


##Hinweis Code-Analyse-Tools
Mit der Verison 1.0-Beta7 habe ich Code-Anlayse-Tools, wie z.B. `PHPUnit` eingebaut. Dafür schein es am Einfachsten
zu sein, eine `composer.json` zu erstellen und die Hilfsprogramme ins Projekt zu installieren. Leider kann durch den
AutoLoader im Redaxo-Core aber dazu kommen, dass sich die Seite in einen Timeout läuft. Es werden alle Dateien
- auch die in `vendor`-Ordnern - analysiert und u.U. eingebunden. Sollte man also in den Fall rennen, kann man den
`vendor`-Ordner in diesem Addon einfach löschen, sofern vorhanden. Wenn er nicht vorhanden ist, so war ich auch nicht
der Übeltäter ;-)

Am Besten führt man die Tests komplett separat durch. Generell sind sie eh nur für mich, um ungenutzten Code bzw.
(SQL-)Fehler zu finden.

