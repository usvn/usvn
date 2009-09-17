#!/usr/bin/env bash

if [ "$#" -ne "0" ]; then
  cmd="bash $(basename $0)"
  if [ "$(basename "$(pwd -P)")" = "locale" ]; then
      cat <<EOF
  usage: $cmd
EOF
  else
      cat <<EOF
  usage: cd \$USVNPATH/app/locale && $cmd
EOF
  fi
fi

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

