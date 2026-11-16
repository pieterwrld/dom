---
name: deploy
description: Deploy the dom-theme and plugins to the production server via SSH
---

# Deploy to Production

## Prerequisites
- SSH access configured (key-based auth)
- Server path: `/var/www/deoutfitman.com/wp-content/`
- WP-CLI installed on server

## Steps

1. Ensure all changes are committed and pushed to `main`:
```bash
git status
git add -A
git commit -m "Deploy: <description>"
git push origin main
```

2. SSH into the server and pull latest theme:
```bash
ssh user@server "cd /var/www/deoutfitman.com/wp-content/themes/dom-theme && git pull origin main"
```

3. If CSS changed, rebuild Tailwind on server:
```bash
ssh user@server "cd /var/www/deoutfitman.com/wp-content/themes/dom-theme && npx tailwindcss -i assets/css/input.css -o assets/css/style.css --minify"
```

4. Deploy plugins (if changed):
```bash
ssh user@server "cd /var/www/deoutfitman.com/wp-content/plugins && git pull origin main"
```

5. Clear all caches:
```bash
ssh user@server "cd /var/www/deoutfitman.com && wp cache flush && wp rewrite flush && wp transient delete --all"
```

6. Verify the site loads:
```bash
curl -s -o /dev/null -w '%{http_code}' https://deoutfitman.com
```

## Rollback
If something breaks:
```bash
ssh user@server "cd /var/www/deoutfitman.com/wp-content/themes/dom-theme && git checkout HEAD~1"
```

## Theme Activation (one-time switch)
```bash
ssh user@server "cd /var/www/deoutfitman.com && wp theme activate dom-theme"
```
