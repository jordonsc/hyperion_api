Development Quirks
==================

Unit Tests Fail
---------------
Try:

* Drop database completely and recreate it - sometimes `doctrine:schema:update` doesn't work properly
    * `app/console doctrine:database:drop --force; app/console doctrine:database:create; app/console doctrine:schema:update --force`
* Remove everything in the cache foler - sometimes the JMS Serializer cache doesn't clear properly
    * `rm -rf app/cache/*`

