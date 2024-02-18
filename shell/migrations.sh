#!/bin/bash
for file in source/Migrations/*.php; do
    php "$file"
done