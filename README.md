completely overhauled for compatibility with MW 1.32.x

All this extension needs to do is take in a "Unit Number" parameter

Parse through the content from
https://www.familysearch.org/wiki/en/api.php?action=query&pageids=137044&prop=revisions&rvprop=content&format=xml

And return the URL associated with the Unit Number.  Note, you don't actually have to use and parse XML. It's probably easier to parse the JSON.

This should all be done at an endpoint similar to
https://www.familysearch.org/wiki/en/extensions/FsApi/FsApiExtension.php?action=unitNbrToFhcWikiPage&unitNbr=3331776 to maintain backwards compatibility (until clients can change their code).

Results should be returned as XML (optionally add json return format for future compatibility)

index.php provides all the functionality required.
