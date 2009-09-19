#!/bin/sh

old_pwd=`pwd`

echo 'Exportation du projet dans /tmp/usvn ...'
rm -rf /tmp/usvn
svn export https://svn.usvn.info/usvn/trunk /tmp/usvn
if [ ! -d /tmp/usvn ]; then
	echo "Erreur d'exportation du projet !"
	exit
fi

echo 'Suppression des fichiers inutiles ...'
cd /tmp/usvn
for i in `find . "(" -type d -and -name "*Test.php" ")" | grep -v Zend`; do
	rm -f "$i"
done
rm -rf build.xml install* usvn.esproj create_archive.sh

echo 'Creation des archives tar.gz et zip ...'
version=`cat config/config.ini.exemple | grep -E "^version" | cut -d'"' -f2`
cd "$old_pwd"
tar cvzf "usvn-$version.tgz" /tmp/usvn
zip -r "usvn-$version.zip" /tmp/usvn

rm -rf /tmp/usvn