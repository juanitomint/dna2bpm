#!/bin/bash
# Rename files to new format uppercase first letter
echo "Migrating: $1"
cd $1
for i in *.php; do
    echo "Processing: $i -> ${i^}";
    mv "$i" "${i^}";
done;