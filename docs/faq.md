# FAQ

### Why not detect changes in templates and re-build only those?
Detecting changes inside templates and covering all the edge cases that come with that approach would needlessly increase the complexity of the codebase. The brute-force approach of wiping the output folder and building everything might seem wasteful, but it ensures that your latest build is always correct, keeps the code simple and doesn't impact performance that much (1000 page sites are still built under 1s).

### Where's the pagination?
Same reason as the last question, offering collections, pagination and dynamic page generation would introduce complexity. You can group pages using the `site.select` and `site.dir` methods.

### Why can't I write my content in Markdown files?
You can write the markdown inside a markdown block inside a Twig template.

### Why is there no front matter?
I wanted to avoid mixing YAML and Twig.

### How fast is it?
Per build:

* 1~100 pages: 50~300ms
* 100~1000 pages: 300~1000ms
* 1000~10000 pages: 1s~10s

The increase in build time is largel