#!/usr/bin/env bash
sed -i -E 's/\s+$//g' packages/blog/config/data/201*/* *.yaml *.md
