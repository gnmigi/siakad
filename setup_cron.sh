#!/bin/bash
# Laravel Scheduler Cron Job
# Add this to your crontab: crontab -e
# Then add this line:
# * * * * * cd C:\laragon\www\siakad-pt.internal-dev.id && php artisan schedule:run >> /dev/null 2>&1

echo "Setting up Laravel Scheduler cron job..."
echo "Current directory: C:\laragon\www\siakad-pt.internal-dev.id"
echo ""
echo "To setup automatic payment verification, add this to your crontab:"
echo "1. Run: crontab -e"
echo "2. Add this line:"
echo "* * * * * cd C:\laragon\www\siakad-pt.internal-dev.id && php artisan schedule:run >> /dev/null 2>&1"
echo ""
echo "This will run the Laravel scheduler every minute, which will:"
echo "- Check for pending payments every 5 minutes"
echo "- Auto-verify payments that are settled in Midtrans"
echo "- Generate receipts automatically"
echo ""
echo "Manual commands available:"
echo "- php artisan payments:auto-verify (run verification manually)"
echo "- php artisan schedule:list (see all scheduled tasks)"
echo ""
