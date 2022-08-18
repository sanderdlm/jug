# FAQ

### Why not detect changes in templates and re-build only those?
Detecting changes inside templates and covering all the edge cases that come with that approach would needlessly increase the complexity of the codebase. The brute-force approach of wiping the output folder and building everything might seem wasteful, but it ensures that your latest build is always correct, keeps the code simple and doesn't impact performance that much (1000 page sites are still built under 1s).

### Where's the pagination?
Same reason as the last question, offering collections, pagination and dynamic page generation would introduce complexity, and I have no intention of maintaining that code. You can group pages using the `site.select` and `site.dir` methods. Those should get you a long way. 

### Why can't I write my content in Markdown files?
You can. You just have to write the markdown inside a markdown block inside a Twig template. I don't see the point of mapping content files to templates when you could just do that when you're writing the content.

### Why is there no front matter?
I wanted to keep it simple. Everything is done in Twig. No other parsing.

### How fast is it?
Per build:

* 1~100 pages: 50~300ms
* 100~1000 pages: 300~1000ms
* 1000~10000 pages: 1s~10s

It's intended for smaller sites, obviously. But you should easily be able to build thousands of pages in under a second.