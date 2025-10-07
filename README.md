[english](README_en.md)

# Usage Check

Dieses Addon sucht nach Bildern, Module und Templates, die nicht (mehr) verwendet werden.

Mit der Version 2.0 wird die Unterstützung für Redaxo 4 entfernt. Vorrangig passiert das wegen (für mich nicht
nachvollziehbaren) Zeichensatz-Problemen in diversen Redaxo 5 Instanzen.

Mit der Version 2.1 wandern die Bearbeiten-Links in eine Detail-Seite. Dadurch kann man im SQL die Anweisung
`group_concat` verhindern, dessen Ergebnis u.U. abgeschnitten wird.

## Installation

### Standard-Installation
Normalerweise kann man das Addon normal über das Redaxo-Backend installieren. Im Normalfall sollte man als Benutzer auch
diesen Weg wählen.

### Manuelle Installation
Eine Ausnahme stellt das Testen von Beta-Funktionalitäten dar. Dann muss man ganz "old school" die Dateien laden und an
die richtige Stelle kopieren.

Dafür nun folgende Hinweise:

Das Repo beinhaltet direkt alle Dateien für das Verzeichnis `redaxo/src/addons/usage_check`

Das Verzeichnis selber (`usage_check`) ist hier nicht enthalten, da man das Addon sonst nicht direkt an die richtige
Stelle auschecken könnte.

Man kann auch das hier generierte ZIP herunterladen und an die richtige Stelle kopieren.

Dann muss man das Addon nur noch über das Redaxo-Backend installieren und aktivieren.

## Kompatibilität
- PHP Version: __8.1__ oder höher
- der neuesten Redaxo-Version zum Zeitpunkt des Releases (Mehrere Instanzen zu betreuen, schaffe ich zeigtlich nicht
mehr.)
