#!/usr/bin/env bash
sed -i -E 's/\s+$//g' source/_posts/201*/* *.yml *.md packages/*/*.md
