#!/bin/bash
echo "Creating Module: $1";
mkdir $1;
# assets
mkdir "$1/assets";
touch "$1/assets/.gitattributes"
# models
echo "Creating Module Models";
mkdir "$1/models";
touch "$1/models/.gitattributes"
# views
echo "Creating Module Views";
mkdir "$1/views";
touch "$1/views/.gitattributes"
# Controllers
echo "Creating Module Controllers";
mkdir "$1/controllers";
touch "$1/controllers/.gitattributes"
echo "Setting up Assets controller"
cp ./user/controllers/Assets.php $1/controllers
default_controller="$1/controllers/${1^}.php"
if [ ! -f $default_controller ]; then
echo "Setting up Default Controller";
cp ./controller_scaffold.php $default_controller
#prepare command to replace in file
command="s/Scaffold/${1^}/g"
sed -i $command $default_controller
else 
echo "Default controller already existed";
fi