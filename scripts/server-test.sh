#!/bin/bash

cd ~/usvn
svn update
rm -Rf test
phing test
if [ $? != 0 ]
then
echo "http://testunit.usvn.info/" | mail -s 'Echec des tests unitaire' pfe-subversion@googlegroups.com
fi
#phing cover
