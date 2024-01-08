@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../magento/magento2-functional-testing-framework/bin/mftf
php "%BIN_TARGET%" %*
