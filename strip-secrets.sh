#!/bin/sh
git filter-branch --env-filter '
if [ "$GIT_COMMIT" = "d891fc8ec084314cfa3127dbda3792f468e90713" ]; then
    git rm --cached -f .env process_login.php process_register.php
    echo "Secrets removed from commit $GIT_COMMIT"
fi
' --tag-name-filter cat -- --all
