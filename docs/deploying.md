# Deploying your Jug site

## Github pages

You can use a simple, custom Github Action workflow to build your site & push the output to Github Pages.

Create a `.github/workflows/deploy.yml` file in your repository with the following content:
```yaml
name: Build & deploy
on:
  push:
    branches: ["main"]

  workflow_dispatch:

permissions:
  contents: read
  pages: write
  id-token: write

concurrency:
  group: "pages"
  cancel-in-progress: false

jobs:
  build:
    environment:
      name: github-pages
      url: ${{ steps.deployment.outputs.page_url }}
    runs-on: "ubuntu-latest"
    steps:
    - name: "Checkout code"
      uses: "actions/checkout@v4"

    - name: Setup Pages
      uses: actions/configure-pages@v4
      
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'

    - name: "Install dependencies with Composer"
      uses: "ramsey/composer-install@v2"

    - name: "Build site"
      run: "vendor/bin/jug build"
      
    - name: Upload artifact
      uses: actions/upload-pages-artifact@v3
      with:
        path: './output'
        
    - name: Deploy to GitHub Pages
      id: deployment
      uses: actions/deploy-pages@v4
```
This workflow will build & deploy the content of your `source` folder to your Github Pages site for that repository.
## Your own host
```shell
#!/bin/bash
host="123.456.1.1"
domain="mysite.com"
rsync -a -P output/* root@$host:/var/www/$domain/
```

## Netlify, Vercel, Render, etc..

Push to a branch and then set your provider to deploy from your output folder.