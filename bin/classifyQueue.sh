#!/bin/bash

while true
do
    /usr/bin/php /var/tools/classifier/bin/classifier.php classifyMany -c https://api.webinsights.info/collection/job/pop
done
