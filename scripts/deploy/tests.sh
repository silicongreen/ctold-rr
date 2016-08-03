#!/bin/bash

echo "Starting tests..."

export APPLICATION_ENV=staging; cd /var/www/cysoco/; php55 vendor/bin/codecept run
