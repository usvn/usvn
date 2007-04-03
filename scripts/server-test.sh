#!/bin/bash

cd ~/usvn
svn update

phing test
if [ $? != 0 ]
then
echo "http://testunit.usvn.info/\n\n" | mail -s 'Echec des tests unitaire' pfe-subversion@googlegroups.com
fi
phing cover
