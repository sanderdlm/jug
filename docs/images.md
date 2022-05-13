# Optimized images

Jug will look for optipng and jpegoptim on your local system and both will be used on all images when you build your site. A very basic cache of which images have already been optimised is kept in a json file to prevent multiple runs on the same images.

Linux:
```
sudo apt install jpegoptim optipng
```