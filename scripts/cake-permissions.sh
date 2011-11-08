#!/bin/bash

if [ $# -ne 1 ]
then
    echo "Usage: $0 APPDIR"
    exit 1
fi

if [ `id -u` -ne 0 ]
then
	SUDO="sudo"
else
	SUDO=""
fi

APPDIR="$1"

if ( `groups www-data &> /dev/null` )
then
	GROUP="www-data"
elif ( `groups apache &> /dev/null` )
then
	GROUP="apache"
else
	echo "$0: Web server user unknow"
	exit 1
fi

CONFIG="config"
DIRS=""
DIRS="$DIRS tmp/cache/models"
DIRS="$DIRS tmp/cache/persistent"
DIRS="$DIRS tmp/cache/views"
DIRS="$DIRS tmp/cache/acl"
DIRS="$DIRS tmp/cache/data"
DIRS="$DIRS tmp/logs"
DIRS="$DIRS tmp/sessions"
DIRS="$DIRS tmp/tests"
DIRS="$DIRS webroot/files"
DIRS="$DIRS webroot/files/resized"
DIRS="$DIRS webroot/files/plots"
DIRS="$DIRS webroot/files/scde"
DIRS="$DIRS webroot/files/hydro"

for DIR in $DIRS
do
	echo $DIR
	DIR=`echo "$DIR" | sed 's/\/\+/\//g'`
	DIR=`echo "$DIR" | sed 's/\/$//'`

	mkdir -p "$APPDIR/$DIR"
	touch "$APPDIR/$DIR/empty"

	SUBDIR="$APPDIR"

	for TOKEN in `echo "$DIR" | tr '/' ' '`
	do
		SUBDIR="$SUBDIR/$TOKEN"
		$SUDO chmod 770 "$SUBDIR"
		$SUDO chgrp -R $GROUP "$SUBDIR"
	done
done

for EXAMPLE in "$APPDIR"/"$CONFIG"/*.php.example
do
	echo $EXAMPLE
	FILE="`echo "${EXAMPLE%.*}"`"

	if [ ! -f "$FILE" ]
	then
		cp "$EXAMPLE" "$FILE"
	fi

	$SUDO chgrp $GROUP "$FILE"
	$SUDO chmod 660 "$FILE"
done
