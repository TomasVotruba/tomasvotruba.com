#!/usr/bin/env bash

# Build settings
REPOSITORY="https://${GH_TOKEN}@github.com/TomasVotruba/tomasvotruba.cz.git"

# Git identity
git config --global user.email "travis@travis-ci.org"
git config --global user.name "Travis"

# Generate API
./vendor/bin/sculpin generate

# Commit & push
cd output
git init
git add .
git commit -m "Regenerated output"
git push --force --quiet "${REPOSITORY}" master:gh-pages > /dev/null 2>&1
