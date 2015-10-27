
<ul>
	<li>Bugfix: If there aren't any XFORM-tables, the database query analyzing unused files didn't work.</li>
	<li>Also generate a link to the sources, even if an element is not used</li>
	<li>Check the user rights on each site request.</li>
	<li>Gererating the ISO encoded language files needed to translate the backend interface (only if needed / needs writing permissions in the <code>lang</code> folder)</li>
	<li>Encoding bugfix. Using the <code>concat</code> function with <code>integer</code> and <code>varchar</code> columns are leading to encoding problems in MySQL &lt; 5.5</li>
</ul>
