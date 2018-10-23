<ul>
	<li>Refactoring: renamed functions connectet with XForm to YForm</li>
	<li>Text changes: removed connections to XForm.</li>
	<li>Deleted unsued code</li>
	<li>Bugfix: inactive but used templates don't have any edit link</li>
	<li>
		Bugfix: If the langauge is something different than German or Englisch, the english texts should appear.<br />
		(This is not possible for the menu)
	</li>
	<li>
		PHP-Error: Warning: count(): Parameter must be an array or an object that implements Countable<br />
		Perhaps, counting the errors on addon boot didn't work properly before the change.
	</li>
	<li>Building a detail page for images avoiding <code>group_concat</code> which could be buggy.</li>
	<li>Bugfix translation index on action page</li>
</ul>
