# What it is not

* Jug does not offer pagination
* It does not use front-matter
* It does not match markdown files to templates

## What it is

It is a 1-to-1 Twig to HTML static site generator. If you want to end up with `/foo/bar/page.html`, you have to write `/foo/bar/page.twig`. You want 20 blog posts? Each post is a Twig file.

All markdown content goes straight inside the template.

All metadata is defined using `set` inside the template.
