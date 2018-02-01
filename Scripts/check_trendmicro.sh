#!/bin/bash
# return 1 if is OK
# else return 0
if [ -z "$1" ]; then
    echo "Usage: ${0##*/} IP_ADDRESS"
    exit 2
fi
content=$(curl -k --silent -A "Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:51.0) Gecko/20100101 Firefox/51.0" https://www.ers.trendmicro.com/reputations)

tokenfield=$(echo $content | grep -Po 'Token\]\[fields\]" value="\K(.{43})')
tokenkey=$(echo $content | grep -Po 'Token\]\[key\]" value="\K(.{40})')

tmp=$(curl -k --silent -A "Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:51.0) Gecko/20100101 Firefox/51.0" https://www.ers.trendmicro.com/reputations -X POST -d "data[Reputation][ip]=$1&data[_Token][fields]=$tokenfield&data[_Token][key]=$tokenkey" -d "_method=POST")
error=$(echo "$tmp" | grep -c "404 Not Found" )
if [ "$error" -eq 1 ]; then
        echo "2"
        exit 2
else
        echo "$tmp" | grep 'Unlisted in the spam sender list' -c
fi
