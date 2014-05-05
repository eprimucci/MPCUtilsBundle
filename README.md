MPCUtilsBundle
==============

Symfony2 Bundle for Minor Planet Center data parsing


mkdir mpc.downloads-folder
run permissions.sh


APACHEUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data' | grep -v root | head -1 | cut -d\  -f1`
sudo setfacl -R -m u:"$APACHEUSER":rwX -m u:`whoami`:rwX app/cache app/logs downloaded-resources trashcan
sudo setfacl -dR -m u:"$APACHEUSER":rwX -m u:`whoami`:rwX app/cache app/logs downloaded-resources trashcan
    