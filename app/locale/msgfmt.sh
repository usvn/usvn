#!/usr/bin/env bash

MSGFMTBIN=msgfmt

if ! which "$MSGFMTBIN" >/dev/null ; then
  echo need fmt
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

