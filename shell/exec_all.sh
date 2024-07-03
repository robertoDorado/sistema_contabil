#!/bin/bash

if [ -z "$1" ]; then
    echo "Por favor, forneça o caminho do diretório como argumento."
    exit 1
fi

DIRECTORY="$1"

if [ ! -d "$DIRECTORY" ]; then
    echo "O diretório especificado não existe."
    exit 1
fi

for FILE in "$DIRECTORY"/*.php; do
    php "$FILE"
done
