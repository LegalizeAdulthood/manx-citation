[![Test workflow](https://github.com/LegalizeAdulthood/manx-citation/actions/workflows/test.yml/badge.svg)](https://github.com/LegalizeAdulthood/manx-citation/actions/workflows/test.yml)

# Manx Citations

The [Terminals Wiki](http://terminals-wiki.org) uses a template to create
links to documents in the [manx database](https://manx-docs.org).  Because
the form of the document URLs may change over time, the template is used
to isolate pages from those changes.  The single source of truth for how
such URLs are constructed is in the template.

This code exists in two forms: a MediaWiki extension (WIP) and a NodeJS
script.

## MediaWiki Extension

The MediaWiki extension has two parts to it.  First, the PHP code
integrates into the wiki instance to allow javascript code in the browser
to send a manx URL to the extension and have it return the appropriate
citation template text.  A piece of javascript integrates into the editing
toolbar to prompt the user for the URL, make the API call to the server
and insert the resulting text.

The extension javascript and PHP code are well covered with unit tests
that run in a github action.

## NodeJS Script

The nodejs script takes a manx URL and emits a manx citation
template that is suitable for use in the
[Terminals Wiki](http://terminals-wiki.org).  Example:

```
> node citation.js http://manx.classiccmp.org/details.php/11,21178
{{manx details|11,21178|ADM-31 Brochure}}, May, 1978
```

The script fetches the URL and uses it to identify the publication name
(ADM-31 Brochure) and the date (May, 1978).

On Windows, it can be handy to simply feed the output of the script
into `clip` to get the output onto the clipboard.  The provided batch
file `cite.bat` is a convenience that runs node with the given url and
pipes to `clip`.
