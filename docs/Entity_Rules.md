Rules for CRUD-field Entities
==============================

* All entities must contain a unique 'id' field, as an integer
* All foreign key relationships must have an `xxxx_id` field
* eg: if `account` is the foreign entity, you must also have a field `account_id`
* The `entites.yml` config file MUST be up-to-date with the Hyperion ERD
* See [README](../README.md) for ERD and workflow diagrams
