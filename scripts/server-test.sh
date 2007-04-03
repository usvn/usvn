#!/bin/bash

cd ~/usvn
svn update

phing test
if [ $? != 0 ]
then
mail -s 'Echec des tests unitaire' pfe-subversion@googlegroups.com < test-report/logfile.txt
fi
phing cover
