#!/bin/sh

cd usvn-0.6.2
debuild -S -sa -k"Julien Duponchelle <julien@duponchelle.info>"
cd ..
sudo pbuilder build *dsc