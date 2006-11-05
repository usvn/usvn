#!/bin/bash

cd ..
phing test
if [ $? = 1 ]
then
mail -s 'Echec des tests unitaire' noplay@noplay.net < report/logfile.txt
fi
