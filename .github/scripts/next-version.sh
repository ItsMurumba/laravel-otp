#!/usr/bin/env bash
# Computes the next release version (no leading "v") and prints it on stdout.
# Prints "NONE" when there is nothing to release (no commits since the last
# stable tag and no explicit override).
#
# Usage: next-version.sh <auto|patch|minor|major> [explicit-version]
set -euo pipefail

MODE="${1:-auto}"
EXPLICIT_VERSION="${2:-}"

if [[ -n "$EXPLICIT_VERSION" ]]; then
    echo "${EXPLICIT_VERSION#v}"
    exit 0
fi

# Last *stable* tag only — excludes -rc.N pre-releases.
LAST_STABLE_TAG=$(git tag --list 'v[0-9]*.[0-9]*.[0-9]*' | grep -vE -- '-rc\.' | sort -V | tail -1 || true)

if [[ -z "$LAST_STABLE_TAG" ]]; then
    echo "0.0.1"
    exit 0
fi

COMMITS=$(git log "${LAST_STABLE_TAG}..HEAD" --format='%s%n%b')

if [[ -z "$(echo "$COMMITS" | tr -d '[:space:]')" ]]; then
    echo "NONE"
    exit 0
fi

BUMP="$MODE"
if [[ "$MODE" == "auto" ]]; then
    if echo "$COMMITS" | grep -qE '^[a-zA-Z]+(\([^)]*\))?!:' || echo "$COMMITS" | grep -q 'BREAKING CHANGE:'; then
        BUMP="major"
    elif echo "$COMMITS" | grep -qE '^feat(\([^)]*\))?:'; then
        BUMP="minor"
    else
        BUMP="patch"
    fi
fi

IFS='.' read -r MAJOR MINOR PATCH <<< "${LAST_STABLE_TAG#v}"

case "$BUMP" in
    major)
        echo "$((MAJOR + 1)).0.0"
        ;;
    minor)
        echo "${MAJOR}.$((MINOR + 1)).0"
        ;;
    patch)
        echo "${MAJOR}.${MINOR}.$((PATCH + 1))"
        ;;
    *)
        echo "Unknown bump type: $BUMP" >&2
        exit 1
        ;;
esac
