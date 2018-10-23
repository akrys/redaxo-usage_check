<ul>
	<li>Refactoring: Funktionen, die sich auf XForm beziehen in YForm umbenannt</li>
	<li>Textänderungen: Verweise auf XForm entfernt.</li>
	<li>Aufräumaktion. Nicht benötigten Code entfernt</li>
	<li>Bugfix: inatktive, genutzte Templates haben keinen Bearbeiten-Link</li>
	<li>
		Bugfix: Sofern die Sprache weder Deutsch noch Englisch ist, sollten die englischen Texte genommen werden.<br />
		(Für die Menüpunkte ist das leider nicht möglich)
	</li>
	<li>
		PHP-Fehler: Warning: count(): Parameter must be an array or an object that implements Countable<br />
		Möglicherweise hat das Zählen der Fehler beim Addon-Boot vorher schon nicht richtig funktioniert.
	</li>
	<li>Detailseite für Bilder. Dort wird <code>group_concat</code> mit den dazu gehörigen Nebenwirkungen nicht mehr genutzt.</li>
	<li>Bugfix falscher Index bei einer Übersetzung auf der Aktionen-Seite</li>
</ul>
