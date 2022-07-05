# Draft: Search

When it comes to search on static sites, there are lots of really great solutions for search, like Algolia. Most of them work with some sort of search index that is generated at build time.

Here's a quick rundown of how generate such a search index and integrate it with [Lunr]() to provide search on your static site.

## Build the search index

First we need to generate a search index. This is basically a JSON file with an entry for each page on our static site. Later, we'll pass this JSON file to our JS, so we can search in it.

In Jug, you can easily dump a full search index file in the `AfterBuild` event. Create an `events.php` in the root of your site folder and add the following:

```php
<?php

declare(strict_types=1);

use Jug\Event\AfterBuild;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\Event;

return static function (EventDispatcher $dispatcher): void {
    $dispatcher->addListener(AfterBuild::NAME, function (Event $event) {
        /** @var AfterBuild $afterBuild */
        $afterBuild = $event;

        $outputPath = $afterBuild->site->config->get('output') . DIRECTORY_SEPARATOR;

        $pageData = [];

        foreach ($afterBuild->site->pages as $page) {
            $pageContent = file_get_contents($outputPath . $page->output->relativePath);

            $pageData[] = [
                "title" => $page->context['title'],
                "url" => $page->output->relativePath,
                "content" => trim(preg_replace('/\s\s+/', ' ', strip_tags($pageContent))),
            ];
        }

        file_put_contents($outputPath . 'search_data.json', json_encode($pageData));
    });
};
```

After every build, we'll get an up-to-date JSON file dumped in our output folder.

## Adding the search markup to our site

In this example, I'm using Bootstrap, but you can provide any kind of mark-up you want.

```html
<div data-role="search">
    <label for="search">Search</label>
    <input class="form-control" type="text" id="search" placeholder="Search..." autocomplete="off">
    <div class="dropdown">
        <ul class="dropdown-menu"></ul>
    </div>
</div>
```
You can add this snippet throughout your site.

## Adding the JS

Next, we're going to perform the actual searching with Javascript. First, we fetch the content of the JSON file and store it as a search index (in memory) for later use. Next, we use the keyup event on our search input to perform the actual search and render the results in a dropdown list.
```js
class Search {
    constructor (element) {
        this.searchInput = element.querySelector('input')
        this.searchResults = element.querySelector('ul')
        this.searchData = []

        this.populateSearchIndex()
        this.bindEvents()
    }

    populateSearchIndex () {
        fetch('/search_data.json')
            .then(response => response.json())
            .then(data => {
                data.forEach(object => {
                    this.searchData[object.url] = object
                })

                this.lunr = lunr(function() {
                    this.field('url');
                    this.field('title', { boost: 10 });
                    this.field('content');

                    data.forEach(object => {
                        this.add(object)
                    }, this)
                })
            })
    }

    bindEvents() {
        this.searchInput.addEventListener('keyup', (event) => {
            event.preventDefault();

            if (event.keyCode === 27) {
                this.hideResults()
                return;
            }

            const query = this.searchInput.value;
            const results = this.lunr.search(query)
            this.display(results)
        })

        document.querySelector('html').addEventListener('click', event => {
            this.hideResults()
        });
    }

    hideResults () {
        this.searchResults.style.display = 'none'
    }

    showResults () {
        this.searchResults.style.display = 'block'
    }

    display (results) {
        this.searchResults.innerHTML = '';

        if (results.length > 0) {
            results.forEach(result => {
                const item = this.searchData[result.ref];
                const appendString = '<li class="dropdown-item"><a href="' + item.url + '">' + item.title + '</a></li>';

                this.searchResults.insertAdjacentHTML('beforeend', appendString);
            });
        } else {
            this.searchResults.innerHTML = '<li class="dropdown-item"><a>No results found</a></li>';
        }

        this.showResults()
    }
}

const searchElement = document.querySelector('[data-role="search"]')
new Search(searchElement)
```

This is by no means the only way to structure a basic search. Feel free to use this guide as inspiration and tweak it however you want.