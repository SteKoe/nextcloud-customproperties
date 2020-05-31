#!/usr/bin/env bash

set -o errexit
set -o nounset
set -o pipefail

main() {
  local version
  version=$1

  downloadAndExtract $version
  buildJavaScript
  signSource
  buildArchive
  createSignature

  rm v$version.tar.gz
  rm -rf customproperties
}

downloadAndExtract() {
  local version
  version=$1

  wget https://github.com/SteKoe/nextcloud-customproperties/archive/v$version.tar.gz -O v$version.tar.gz
  tar -zxf v$version.tar.gz

  mv nextcloud-customproperties-$version customproperties
}

buildJavaScript() {
  echo "Build JavaScript..."
  (cd customproperties/src && npm ci && npm run build)
}

signSource() {
  local cid

  rm -rf customproperties/.git customproperties/src

  cid=$(docker run -d -v $(pwd)/customproperties:/app -v ~/.nextcloud/certificates/:/nextcloud/certificates/ nextcloud)
  echo "Sign sources using container '$cid'..."

  echo "Waiting for NextCloud to be initialized..."
  until docker logs $cid 2>&1 | grep "Initializing finished" >/dev/null; do
    sleep 1
    echo "Waiting..."
  done
  echo "NextCloud initialized!"

  echo "Signing app..."
  docker exec -i $cid /bin/bash -c "chmod -R +w /app" || true
  docker exec -i $cid php occ integrity:sign-app --privateKey=/nextcloud/certificates/customproperties.key --certificate=/nextcloud/certificates/customproperties.crt --path=/app || true
  echo "Tidy up..."
  docker rm -f $cid >/dev/null
}

buildArchive() {
  echo "Build archive..."
  (tar -czf customproperties.tar.gz \
    --exclude=./src/node_modules/ \
    --exclude=./.git/ \
    --exclude=./src/ \
    customproperties/)
}

createSignature() {
  echo "Create signature..."
  openssl dgst -sha512 -sign ~/.nextcloud/certificates/customproperties.key customproperties.tar.gz | openssl base64
}

main $@
