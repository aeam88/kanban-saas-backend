#!/bin/bash

# Cache config and routes for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (Safe to run multiple times, applies only new ones)
php artisan migrate --force

# Start Apache in the foreground
apache2-foreground
