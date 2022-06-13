# Draft: Sitemap

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>{{ site.config.url }}</loc>
    <lastmod>{{ site.config.timestamp|date('Y-m-d') }}</lastmod>
    <changefreq>daily</changefreq>
    <priority>0.8</priority>
  </url>
  {% for page in site.pages %}
  <url>
    <loc>{{ site.config.url }}{{ page.relativePath }}</loc>
    <lastmod>{{ site.config.timestamp|date('c') }}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>1.0</priority>
  </url>
  {% endfor %}
</urlset>
```