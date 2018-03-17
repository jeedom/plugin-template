#!/bin/bash
for file in `find . -name *.py`; 
  do 
    pylint  --disable=C --disable=R --disable=W $file
  done 
