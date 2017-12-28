[english](README_en.md)

# Usage Check

Dieses Addon sucht nach Bildern, Module und Templates, die nicht (mehr) verwendet werden.

Mit der Version 2.0 wird die Unterstützung für Redaxo 4 entfernt. Vorrangig passiert das wegen (für mich nicht
nachvollziehbaren) Zeichensatz-Problemen in diversen Redaxo 5 Instanzen.

## Installation

###Standard-Installation
Normalerweise kann man das Addon normal über das Redaxo-Backend installieren. Im Normalfall sollte man als Benutzer auch
diesen Weg wählen.


###Manuelle Installation
Eine Ausnahme stellt das Testen von Beta-Funktionalitäten dar. Dann muss man ganz "old school" die Dateien laden und an
die richtige Stelle kopieren.

Dafür nun folgende Hinweise:

Das Repo beinhaltet direkt alle Dateien für das Verzeichnis `redaxo/src/addons/usage_check`

Das Verzeichnis selber (`usage_check`) ist hier nicht enthalten, da man das Addon sonst nicht direkt an die richtige
Stelle auschecken könnte.

Man kann auch das hier generierte ZIP herunterladen und an die richtige Stelle kopieren.

Dann muss man das Addon nur noch über das Redaxo-Backend installieren und aktivieren.

## Kompatibilität
- PHP Version: __5.6__ oder höher
- getestet mit Redaxo __5.0.1__, __5.1__, __5.2__,  __5.3__,  __5.4__,  __5.5__

##Hinweis Code-Analyse-Tools
Mit der Verison 1.0-Beta7 habe ich Code-Anlayse-Tools, wie z.B. `PHPUnit` eingebaut. Dafür schein es am Einfachsten zu
sein, eine `composer.json` zu erstellen und die Hilfsprogramme ins Projekt zu installieren. Leider kann durch den
AutoLoader im Redaxo-Core aber dazu kommen, dass sich die Seite in einen Timeout läuft. Es werden alle Dateien - auch
die in `vendor`-Ordnern - analysiert und u.U. eingebunden. Sollte man also in den Fall rennen, kann man den `vendor`-
Ordner in diesem Addon einfach löschen, sofern vorhanden. Wenn er nicht vorhanden ist, so war ich auch nicht der
Übeltäter ;-)

Am Besten führt man die Tests komplett separat durch. Generell sind sie eh nur für mich, um ungenutzten Code bzw.
(SQL-)Fehler zu finden.

