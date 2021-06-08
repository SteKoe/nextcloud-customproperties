#!/usr/bin/env bash

set -o errexit
set -o nounset
set -o pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" &>/dev/null && pwd)"
VERSION=$(xmllint --xpath 'string(/info/version)' appinfo/info.xml)
CONTAINER_ID=

main() {
  buildJavaScript
  signSource
  buildArchive
  createSignature
  tidyUp
}

buildJavaScript() {
  echo "Build JavaScript..."
  npm i && npm run build
}

signSource() {
  CONTAINER_ID=$(docker run -d --name customproperties-build -v ~/.nextcloud/certificates/:/nextcloud/certificates/ nextcloud)

  echo "Copy app to container '$CONTAINER_ID'..."
  docker cp $(pwd) $CONTAINER_ID:/customproperties
  docker exec -i $CONTAINER_ID /bin/bash -c "cd /customproperties && rm -rf .cache .git .idea node_modules"

  echo "Sign sources using container '$CONTAINER_ID'..."

  echo "Waiting for NextCloud to be initialized..."
  until docker logs $CONTAINER_ID 2>&1 | grep "Initializing finished" >/dev/null; do
    sleep 5
    echo "Waiting..."
  done
  echo "NextCloud initialized!"

  echo "Sign app app..."
  docker exec -i $CONTAINER_ID php occ integrity:sign-app --privateKey=/nextcloud/certificates/customproperties.key --certificate=/nextcloud/certificates/customproperties.crt --path=/customproperties || true
}

buildArchive() {
  echo "Build archive..."
  docker exec -i $CONTAINER_ID tar -czf customproperties_$VERSION.tar.gz \
    --exclude=./src/node_modules/ \
    --exclude=./.git/ \
    --exclude=./.build/ \
    --exclude=./.cache/ \
    --exclude=./.idea/ \
    --exclude=./src/ \
    /customproperties

  echo "Copy archive..."
  docker cp $CONTAINER_ID:/var/www/html/customproperties_$VERSION.tar.gz $(pwd)
}

createSignature() {
  echo "Create signature..."
  openssl dgst -sha512 -sign ~/.nextcloud/certificates/customproperties.key customproperties_$VERSION.tar.gz | openssl base64
}

tidyUp() {
  echo "Tidy up..."
  docker rm -f -v $CONTAINER_ID >/dev/null
}

main $@
