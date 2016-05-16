Sucht nach verwaisten Medienpool-Dateien, nicht verwendete Module und Templates.

<h3> Kompatibilität</h3>
<ul>
	<li>PHP Version: <b>5.3.2</b> oder höher</li>
	<li>
		getestet mit Redaxo
		<b>4.3.2</b>,
		<b>4.4.1</b>,
		<b>4.5</b>,
		<b>4.6.1</b>,
		<b>5.0.1</b>,
		<b>5.1</b>
	</li>
	<li>Der Code funktioniert für Redaxo 4 und Redaxo 5</li>
</ul>

<h3>Hinweis für die Sprachdateien</h3>

<p>
	Der Ordner <tt>usage_check/lang</tt> braucht Schreibrechte für den PHP bzw. Apache User.
</p>

<p>
	Grund dafür ist, dass von mir nur die Dateien <tt>de_de_utf8.lang</tt> und <tt>en_gb_utf8.lang</tt> gepflegt werden.
</p>

<p>
	Jetzt wird es etwas undurchsichtig: Für Redaxo 4 braucht es den Inhalt der Dateien <tt>de_de_utf8.lang</tt> und <tt>en_gb_utf8.lang</tt> in <b>ISO-8859-1</b>.
Da diese Dateien in Redaxo 5 aber <b>UTF-8</b> kodiert sein müssen, muss ich diese Dateien je nach verwendeter Redaxo-Version aus den <tt>xx_yy_utf8.lang</tt> Dateien in der richtigen Zeichenkodierung erstellen.<br />
Dafür braucht es dann Schreibrechte am Verzeichnis.
</p>

<p>
	Leider kommt man da nicht drum herum, wenn der Code für beide Redaxo-
	Versionen kompatibel sein soll und man gleichzeitig die Sprachdateien nur 1x
	pflegen will.
</p>
