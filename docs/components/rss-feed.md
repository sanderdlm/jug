# Draft: RSS feed

```xml
<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
    <title><![CDATA[{{ site.title }}]]></title>
    <link href="{{ site.url }}/atom.xml" rel="self"/>
    <link href="{{ site.url }}/"/>
    <updated>{{ site.calculated_date | date('c') }}</updated>
    <id>{{ site.url }}/</id>
    {% if site.author or site.email %}
        <author>
            {% if site.author %}<name><![CDATA[{{ site.author }}]]></name>{% endif %}
            {% if site.email %}<email><![CDATA[{{ site.email }}]]></email>{% endif %}
        </author>
    {% endif %}
    <generator uri="localhost">Your site</generator>
    {% for post in data.posts|slice(0, 10) %}
        <entry>
            <title type="html"><![CDATA[{{ post.title }}]]></title>
            <link href="{{ site.url }}{{ post.url }}"/>
            <updated>{{ post.date|date('c') }}</updated>
            <id>{{ site.url }}{{ post.url }}</id>
            <content type="html"><![CDATA[{{ post.blocks.content|raw }}]]></content>
        </entry>
    {% endfor %}
</feed>
```