#!/bin/sh

old_pwd=`pwd`

echo 'Exportation du projet dans /tmp/usvn ...'
rm -rf /tmp/usvn-1.0
svn export https://svn.usvn.info/usvn/trunk /tmp/usvn-1.0
if [ ! -d /tmp/usvn-1.0 ]; then
	echo "Erreur d'exportation du projet !"
	exit
fi

echo 'Suppression des fichiers inutiles ...'
cd /tmp/usvn-1.0
for i in `find . -name "*Test.php" | grep -v Zend`; do
	rm -f "$i"
done
rm -rf build.xml install* usvn.esproj create_archives.sh

echo 'Creation des archives tar.gz et zip ...'
version=`cat config/config.ini.exemple | grep -E "^version" | cut -d'"' -f2`
cd /tmp
tar cvzf "$old_pwd/usvn-$version.tgz" ./usvn-1.0
zip -r "$old_pwd/usvn-$version.zip" ./usvn-1.0

rm -rf /tmp/usvn-1.0