# Twig

## Custom Twig filters and functions

### Functions

* `asset('some-asset.png)` will return a versioned filename of that asset, e.g. `some-asset-f8d7dk3.png`
* `markdown('this is *markdown*')` will render markdown.
* `dir('source')` will return an iterable list of all filenames in a folder. The key is a clean filename without extension, the value is the full path to the file.
* `highlight('$foo ? $bar : null')` will highlight some code using the PHP `highlight_string` function.
* `data('main.db', 'SELECT * FROM posts)` can be used to select data from a sqlite database.

### Filters

* markdown: same as the function but as a filter. Can be used directly after strings or with an apply block

`'some **markdown** content'|markdown`

Or
```
{% apply markdown %}
 # More markdown!
 
 This could be an entire blog post!
 
 * a list
 * more list items
{% endapply %}
```