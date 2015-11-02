#!/bin/bash

FILE='src/helixware.php'
README='trunk/readme.txt'

echo "checking out and updating the svn branch..."
git checkout -b svn
git pull origin svn
echo "removing src..."
rm -fr src
echo "updating the svn branch..."
svn up
echo "checking out the src folder from master branch..."
git checkout master -- src

VERSION=`egrep -o "Version:\s+\d+\.\d+\.\d+" $FILE | egrep -o "\d+\.\d+\.\d+"`

if [[ -z "$VERSION" ]]; then
	echo "version not set, halting."
else
	echo "removing tag $VERSION..."
	svn rm --force tags/$VERSION
	echo "removing trunk..."
	svn rm --force trunk
	svn ci -m "updating trunk (1 of 2)"
	mv src trunk
	echo "Setting the stable tag in $README..."
	sed -i '' "s/Stable tag: .*/Stable tag: $VERSION/g" $README
	svn add trunk
	svn cp trunk tags/$VERSION
	svn ci -m "updating trunk (2 of 2)"

	git add -A
	git commit -m "bump to $VERSION" -a
	git push origin svn
fi


