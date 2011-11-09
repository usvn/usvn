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
echo 'Exportation du projet dans /tmp/usvn ...'
rm -rf "$TMP_PATH" || die "Erreur de supression de $TMP_PATH"
svn export "$CHECKOUT_URL" "$TMP_PATH" | sed 's/^/EXPORT: /' || die "Erreur d'exportation $CHECKOUT_URL dans $TMP_PATH"

# Nettoyage
echo 'Suppression des fichiers inutiles ...'
cd "$TMP_PATH"
# Test files
find . -name 'Zend' -prune -or -name "*Test.php" -exec rm -vf {} \; | sed 's/^/RM: /'
# Misc Files
rm -rvf build.xml epitech usvn.esproj create_archives.sh | sed 's/^/RM: /'

# Creation des Archives
echo 'Creation des archives tar.gz et zip ...'
version=`cat config/config.ini.example | grep -E "^version" | cut -d'"' -f2`
cd "$TMP_ROOT"
tar cvzf "$old_pwd/usvn-$version.tgz" "$USVN" | sed 's/^/TAR: /'
zip -r "$old_pwd/usvn-$version.zip" "$USVN" | sed 's/^/ZIP: /'

rm -rf "$TMP_PATH"
