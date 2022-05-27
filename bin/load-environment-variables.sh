#!/usr/bin/env bash

# Load the environment variables from .env into the current shell.
#
# This script is only meant for use on a local system, to simplify
# setting the relevant environment variables before the application
# is deployed.
set -a
source .env
set +a
