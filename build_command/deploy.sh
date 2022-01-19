#!/bin/bash
# $ deploy.sh dev release/v1.0.0

if [ $# != 2 ]; then
  echo "Only 2 argument is valid."
  exit 1
else
  ansible="/repo/docker/provision/ansible/"

  docker exec -it laravel_php ansible-playbook -i ${ansible}hosts --ask-vault-pass --extra-vars "env=${1} branch=${2}" ${ansible}deploy.yml
fi