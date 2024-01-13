#!/bin/bash

aws s3 cp .env.production s3://environment-laravel/env/.env &&
./add_env_prod_to_instances.sh
