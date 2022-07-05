# Draft: RSS feed

```xml
<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
    <title><![CDATA[{{ site.config.get('title') }}]]></title>
    <link href="{{ site.config.get('baseUrl') }}/atom.xml" rel="self"/>
    <link href="{{ site.config.get('baseUrl') }}/"/>
    <updated>{{ site.config.get('date')|date('c') }}</updated>
    <id>{{ site.config.get('baseUrl') }}/</id>
    {% if site.config.has('author') or site.config.has('email') %}
        <author>
            {% if site.config.has('author') %}<name><![CDATA[{{ site.config.get('author') }}]]></name>{% endif %}
            {% if site.config.has('email') %}<email><![CDATA[{{ site.config.get('email') }}]]></email>{% endif %}
        </author>
    {% endif %}
    <generator uri="localhost">Your site</generator>
    {% for page in site.pages %}
        <entry>
            <title type="html"><![CDATA[{{ page.context['title'] }}]]></title>
            <link href="{{ site.config.get('baseUrl') }}{{ page.output.relativePath }}"/>
            <updated>{{ site.config.get('baseUrl')|date('c') }}</updated>
            <id>{{ site.config.get('baseUrl') }}{{ page.output.relativePath }}</id>
            <content type="html"><![CDATA[{{ post.blocks.content|raw }}]]></content>
        </entry>
    {% endfor %}
</feed>
```