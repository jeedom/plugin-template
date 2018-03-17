#!/bin/bash
for file in `find . -name *.py`; 
  do 
  echo $files
  if [ $file = "docs/fr_FR/index-template.md" ] || [ $file = "docs/fr_FR/index.md" ]
  then
    echo "skip "$file
  else
    echo "process "$file
    pylint $file
  fi
done 
