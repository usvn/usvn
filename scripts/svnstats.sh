#!/bin/sh
svn log -v --xml > /tmp/logfile.log
mkdir statsvn
java -jar scripts/statsvn.jar /tmp/logfile.log www -output-dir statsvn/html -cache-dir statsvn/cache -exclude Zend/**:medias/js/tools/**:**/*.mo:medias/default/images/CrystalClear/**
