#!/bin/bash

cd ~/usvn
svn update
mv www/Zend ../
phing doc
mv ../Zend www/
