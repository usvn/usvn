#!/usr/bin/env bash

cd "$(dirname $0)" && echo " $(pwd)"

if [ "$#" -ne "0" ]; then
      cat <<EOF
  usage: $cmd
EOF
fi

MSGFMTBIN=msgfmt

if ! which "$MSGFMTBIN" >/dev/null ; then
  echo "$0 cannnot find $MSGFMTBIN"
  echo "exiting"
  exit 1
fi

ls | 
while read dir; do
  ls "$dir" | grep '.po$' |
  while read po; do
    mo="$dir/${po/%.po/.mo}"
    po="$dir/$po"
    if [ "$po" -nt "$mo" ]; then
      echo "$po" "=>" "$mo"
      "$MSGFMTBIN" "-o" "$mo" "$po"
    fi
  done
done

