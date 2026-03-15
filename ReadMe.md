This is a simple nodejs script to take a manx URL and emit a manx citation template that is suitable for use in the [Terminals Wiki](http://terminals.classiccmp.org).  Example:

```
> node citation.js http://manx.classiccmp.org/details.php/11,21178
{{manx details|11,21178|ADM-31 Brochure}}, May, 1978
```

The script fetches the URL and uses it to identify the publication name (ADM-31 Brochure) and the date (May, 1978).

On Windows, it can be handy to simply feed the output of the script into `clip` to get the output onto the clipboard.  The provided batch file `cite.bat` is a convenience that runs node with the given url and pipes to `clip`.
