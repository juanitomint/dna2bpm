#!/bin/bash
# Rename files to new format uppercase first letter
# rename 's/\b(\w)/\u$1/g' *
#   Controllers
find . \
-name "controllers" \
-exec "./rename_php.bat" {} \;

#   Models
find . \
-name "models" \
-exec "./rename_php.bat" {} \;

#   Libraries
find . \
-name "libraries" \
-exec "./rename_php.bat" {} \;
