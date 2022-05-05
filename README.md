## Project Deployment ##
After running the `composer install` command revert the following files:

```bash
# ./pub/.htaccess - checkout due to websites mapping and custom rewrite rules
git checkout ./pub/.htaccess
```

