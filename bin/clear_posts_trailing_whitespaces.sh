#!/usr/bin/env bash
sed -i -E 's/\s+$//g' packages/blog/data/201*/* *.md
