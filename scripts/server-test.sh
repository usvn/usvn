#!/bin/bash

cd ~/usvn
svn update

phing test
if [ $? = 1 ]
then
mail -s 'Echec des tests unitaire' pfe-subversion@googlegroups.com < report/logfile.txt
fi
phing cover