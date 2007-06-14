#!/bin/sh

echo -n 'Release 1 : '
read RELEASE1

echo -n 'Release 2 : '
read RELEASE2



svn export https://svn.usvn.info/usvn/$RELEASE1/www /tmp/USVN_RELEASE1 \
    > /dev/null
svn export https://svn.usvn.info/usvn/$RELEASE2/www /tmp/USVN_RELEASE2 \
    > /dev/null

find /tmp/USVN_RELEASE1 \
    | sort \
    | sed -r 's#^/tmp/USVN_RELEASE1/##g' \
    > /tmp/USVN_RELEASE1.list

find /tmp/USVN_RELEASE2 \
    | sort \
    | sed -r 's#^/tmp/USVN_RELEASE2/##g' \
    > /tmp/USVN_RELEASE2.list

diff -U 3 /tmp/USVN_RELEASE1.list /tmp/USVN_RELEASE2.list \
    | grep -v /tmp/USVN_ \
    | grep -E '^-' \
    | sed -r 's#^-(.*)#<?php @unlink("../../\1"); ?>#'

rm -Rf /tmp/USVN_RELEASE1
rm -Rf /tmp/USVN_RELEASE2

