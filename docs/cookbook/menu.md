# Draft: Menu

You can build all types of navigation menu's using a combination of the following tools:
 * `site.pages`
 * `site.select()`
 * `site.dir`

The easiest way is to tag which pages you want to appear in your menu
```html
<nav>
    <ul>
        {% for item in site.tree %}
            {% if item is iterable %}
            {% for user in item %}
            Hello {{ user }}!
            {% endfor %}
            {% else %}
            {# users is probably a string #}
            Hello {{ users }}!
        {% endfor %}
    </ul>
</nav>
```