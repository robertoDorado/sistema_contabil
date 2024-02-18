#!/bin/bash

# Verifica se foi fornecido um caminho como argumento
if [ -z "$1" ]; then
    echo "Por favor, forneça o caminho do diretório como argumento."
    exit 1
fi

# Obtém o caminho do diretório a partir do primeiro argumento
diretorio="$1"

# Verifica se o diretório existe
if [ ! -d "$diretorio" ]; then
    echo "O diretório especificado não existe."
    exit 1
fi

# Loop para executar os arquivos PHP no diretório
for arquivo in "$diretorio"/*.php; do
    php "$arquivo"
done
