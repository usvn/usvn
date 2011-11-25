#!/bin/sh
TMP_ROOT='/tmp'
USVN='usvn-1.0.2'
TMP_PATH="$TMP_ROOT/$USVN"
CHECKOUT_URL='https://svn.usvn.info/usvn/trunk'

die()
{
  echo
  echo ">>   $*"
  echo
  exit 1
}

old_pwd=`pwd`

# Exportation
echo 'Exportation to /tmp/usvn ...'
rm -rf "$TMP_PATH" || die "Error can't delete $TMP_PATH"
svn export "$CHECKOUT_URL" "$TMP_PATH" | sed 's/^/EXPORT: /' || die "Error can't export $CHECKOUT_URL to $TMP_PATH"

# Cleanup
echo 'Remove unused files...'
cd "$TMP_PATH"
# Test files
find . -name 'Zend' -prune -or -name "*Test.php" -exec rm -vf {} \; | sed 's/^/RM: /'
# Misc Files
rm -rvf build.xml create_archives.sh | sed 's/^/RM: /'

# Creation archives
echo 'Creating tar.gz and zip ...'
version=`cat config/config.ini.example | grep -E "^version" | cut -d'"' -f2`
cd "$TMP_ROOT"
tar cvzf "$old_pwd/usvn-$version.tgz" "$USVN" | sed 's/^/TAR: /'
zip -r "$old_pwd/usvn-$version.zip" "$USVN" | sed 's/^/ZIP: /'

rm -rf "$TMP_PATH"
