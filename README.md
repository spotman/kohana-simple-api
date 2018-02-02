kohana-simple-api
=================

Simple RPC-like API module for Kohana framework.

It was created with scalability in mind, so you`ll start from using one web instance for frontend and backend,
but later switch to multi-instance architecture where frontend make HTTP request to API server without changing the code!

Features
--------

**Integrated JSON RPC 2.0 server**
Write up your models and use them immediately!
Any JSON RPC 2.0 compatible client may use it.
JQuery plugin included in `static-files/api/jquery.jsonRPC.js`

**Predefined model methods** in `ModelCrudApiResource` for:
- getting one item `one`,
- adding/saving one item `save`,
- deleting one item `delete`

Methods `save` and `delete` provide hooks for security checks.

**Transparent recursive converting of the result's custom objects** in JSON-friendly data structures.
It automatically `foreach` objects implementing `Traversable`. Other objects must implement `ApiResponseItemInterface`.

Example: I've created file `application/classes/ORM.php`

```php
<?php

use Spotman\Api\ApiResponseItemInterface;

class ORM extends Kohana_ORM implements ApiResponseItemInterface
{
    /**
     * Default implementation for ORM objects
     * Override this method in child classes
     *
     * @return array
     */
    public function getApiResponseData()
    {
        return $this->as_array();
    }

    /**
     * Default implementation for ORM objects
     * Override this method in child classes
     *
     * @return \DateTimeImmutable|null
     */
    public function getApiLastModified(): ?DateTimeImmutable
    {
        // Empty by default
        return NULL;
    }
}
```

So I may use ORM instances in API models like this:

```php
<?php

use Spotman\Api\ApiModel;

class API_Model_Category extends ApiModel
{
  public function all()
  {
      $categories = $this->model()->find_all();
      return $this->response($categories);
  }
  
  protected function model($id = NULL)
  {
      return ORM::factory('Category', $id);
  }
}
```

**Scaling** (has auth and performance issues now)
- move your API to another web server/instance
- change `client.proxy` in config to `ApiResourceProxyInterface::EXTERNAL`
- set `client.host` to URL of the new API server
- PROFIT!


Installation
------------

1) Copy config to application/config and edit it if needed

2) Create model classes named like API_Model_...

3) Create public methods; each method must return instance of `ApiMethodResponse`; you may use `ApiModel->response()` helper

4) For better code quality and IDE autocomplete I recommend writing some helpers in `application/classes/API.php`

```php
<?php
namespace Application;

class ApiFacade extends \Spotman\Api\ApiFacade {

    /**
     * @return \API_Model_Category
     */
    public function category()
    {
        return $this->get('Category');
    }

    /**
     * @return \API_Model_Subcategory
     */
    public function subcategory()
    {
        return $this->get('Subcategory');
    }

    /**
     * @return \API_Model_Author
     */
    public function author()
    {
        return $this->get('Author');
    }
}

```

5) Write app code using your helpers

```php
<?php
use Application\ApiFacade;

// Instantiate your ApiFacade class via new keyword or via dependency injection
$api = new ApiFacade;

// API call returns instance of ApiMethodResponse
$categoriesResponse = $api->category()->all();

// Getting model response data
$categories = $categoriesResponse->getData();

// And last modified timestamp (if set)
$categoriesLastModified = $categoriesResponse->getApiLastModified();

```

6) Configure frontend for JSON RPC 2.0 API (by default it would be the link [/api/v1/json-rpc](/api/v1/json-rpc))


NOTICE
------

**This module is in beta version so everything may be changed later.**

Currently API do not provide auth implementation (I'm searching for a better way).
But in `internal` mode API models methods are executed in context of your app, so you may use `Auth::instance()->get_user()` to get current user or to get other current application state if needed.

TODO:
-----

1. ~~Remove static methods from API class~~ Done!
2. Introduce API versions
3. Full support of the ExternalApiResourceProxy (add auth and remove performance leaks)
4. Response caching
5. ... (your vision would be greatly appreciated)

LICENSE
-------

[MIT License](LICENSE)
