#!/bin/bash
PHP_FILES=($(ls source/Migrations/*.php))
for FILE in "${PHP_FILES[@]}"; do
    php "$FILE"
done