## Description

Object-oriented LDAP Client written in PHP.

Dependencies: 
* PHP >= 7.0
* [ldap extension](http://php.net/manual/en/intro.ldap.php)

## How to use

### Create an LDAP instance

First of all, you need an object that knows all the connection data and some options for the native LDAP extension.
The options are not required.

    ```php
    use Rimelek\LDAPCLient\LDAP;
    
    $ldap = new LDAP();
    $ldap->setServer('ldap.domain.tld');
    $ldap->setPort(389);  // optional. Default: 389
    
    $ldap->setManagerDn('uid=' . $user->getUsername() . ',ou=people,dc=domain,dc=tld');
    $ldap->setPassword($user->getPassword());

    $ldap->setScope(LDAP::SCOPE_SUB); // for search (*_ONE | *_BASE | *_SUB)
    $ldap->setBaseDn('ou=people,dc=domain,dc=tld'); // for search
    ```
    
### Create LDAP filter

An LDAP filter string follows this syntax:

* Simplest filter: ```(key=value)```
* Multiple filters:
  * or: ```(key1=value1)|(key2>=value)```
  * and: ```(key1=value1)&(key2>=value2)```
* You can also negate it: 
  ```(!((key1=value1)&(key2>=value2)))```

When you need many filters it can be hard to write. Filters are objects here.
    
    ```php
    use Rimelek\LDAPClient\Filter;
    use Rimelek\LDAPClient\OrFIlter;
    use Rimelek\LDAPClient\AndFilter;
    ```
    
* Simplest filter: ```$filter = new Filter('key', 'value')```
* Multiple filters:
  * or: 
  ```php
  $filter = new OrFilter([
      new Filter('key1', 'value1'),
      new Filter('key2', 'value2', Filter::OP_GREATER_THAN),
  ]);
  ```
  * and:
    ```php
    $filter = new AndFilter([
        new Filter('key1', 'value1'),
        new Filter('key2', 'value2', Filter::OP_GREATER_THAN),
    ]);
    ```
* You can also negate it: 
  For now, AndFilter and OrFilter cannot be negated. Until the solution you can do this:
    ```php
    $filter = new OrFilter([
        new Filter('key1', 'value1', true),
        new Filter('key2', 'value2', Filter::OP_GREATER_THAN, true),
    ]);
    ```
Third argument of Filter is the operator. Here are all of them:

* Filter::OP_EQUALITY (=)
* Filter::OP_GREATER_THAN (>=)
* Filter::OP_LESS_THAN (<=)
* Filter::OP_PROXIMITY (~=)
    
### Search

    ```php
    $result = $ldap->search($filter);
    $entries = $result->getEntries();
    
    foreach ($entries as $entry) {
        $attr = $entry->getAttribute();
        echo $attr->getName() . '<br />';
        foreach ($attr as $value) {
            echo  ' - ' . $value . '<br />';   
        }
    }
    ```
    
    
More information coming soon.