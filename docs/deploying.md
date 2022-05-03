# Deploying your Jug site

## Github pages

```
git subtree push --prefix <your-output-folder> origin gh-pages
```

## Your own host
```shell
#!/bin/bash
host="123.456.1.1"
domain="mysite.com"
rsync -a -P output/* root@$host:/var/www/$domain/
```

## Netlify, Vercel, Render, etc..

Push to a branch and then set your provider to deploy from your output folder.