#!/bin/bash

cd ~/usvn
svn update
phing lint > /tmp/lint.txt
if [ $? = 1 ]
then
mail -s 'Echec de lint' pfe-subversion@googlegroups.com < /tmp/lint.txt
fi

phing test
if [ $? = 1 ]
then
mail -s 'Echec des tests unitaire' pfe-subversion@googlegroups.com < report/logfile.txt
fi
