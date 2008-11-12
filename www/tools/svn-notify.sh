#!/usr/bin/env bash

# Script to retrieve commit message and send it to all registered email
# recipients
# @author Klemen Vodopivec <klemen@vodopivec.org>
# @link http://www.usvn.info
# @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
# @copyright Copyright 2008, Klemen Vodopivec
# @since 0.6
# @package usvn
#
# The development of this file and entire USVN mail-notification support was
# supported through industry projects of Faculty of natural sciences and
# mathematics, University of Maribor, Slovenia <http://www.fnm.uni-mb.si>
#
# $Id$
#
# The script reads one email per line from emails file and uses template file to
# form final email message. Template can contain variables at any place. To
# header is added to the message with single recipient. Mail is then sent
# using UNIX sendmail command.
#
# Following variables get replaced with real values when script is called
# - normally from post-commit hook:
#
# %date% - replaced by current date-time as returned by date command
# %author% - commiter
# %repository% - repository name
# %revision% - revision number
# %comment% - author's message when committed
#

if [ "$4" == "" ]; then
    echo "Usage: $0 <repository path> <revision> <emails file> <template file>" >&2
    exit 1
fi

PATH_REPOS=$1
REVISION=$2
FILE_EMAILS=$3
FILE_TEMPLATE=$4
BIN_SENDMAIL="/usr/sbin/sendmail -t "
BIN_SVNLOOK="/usr/local/bin/svnlook"
DATE=$(date)

AUTHOR=$($BIN_SVNLOOK author -r $REVISION $PATH_REPOS)
COMMENT=$($BIN_SVNLOOK log -r $REVISION $PATH_REPOS)
REPOSITORY=$(basename $PATH_REPOS)

if [ "$DEBUG" == "1" ]; then
    BIN_SENDMAIL=cat
fi

if [ -x "$BIN_SENDMAIL" ]; then
    echo "No sendmail binary" >&2
    exit 2
fi

# Send email to each valid address
cat $FILE_EMAILS | while read email; do
    to=$(echo $email | sed -e "s/.*<\([[:alnum:]\.@_-]\+\)>.*/\1/" | \
    egrep "^[[:alnum:]]+[[:alnum:]\._-]*@[[:alnum:]_-]+[[:alnum:]\._-]*$")

    if [ ! -z "$to" ]; then
        cat $FILE_TEMPLATE | sed \
            -e "s|%date%|$DATE|g" \
            -e "s|%author%|$AUTHOR|g" \
            -e "s|%repository%|$REPOSITORY|g" \
            -e "s|%revision%|$REVISION|g" \
            -e "s|%comment%|$COMMENT|g" \
            -e "s|^to.*|To: $EMAIL|gI" \
        | $BIN_SENDMAIL $to
    fi
done

exit 0
