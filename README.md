kohana-simple-api
=================

Simple RPC-like API module for Kohana framework.

It was created with scalability in mind, so you`ll start from using one web instance for frontend and backend,
but you may switch to a multi-instance architecture later where the backend makes HTTP request to the API server with near zero changes in your code!

Features
--------

**Integrated JSON RPC 2.0 server**
Write up your models and use them immediately!
Any JSON RPC 2.0 compatible client may use it.

**Automatic ACl bindings and permissions check**
- API resource => ACL resource
- API method => ACL action

**Result's custom objects are converted transparently and recursively** in JSON-friendly data structures.
It automatically iterates objects implementing `Traversable`. Other objects must implement `ApiResponseItemInterface`.

**Scaling** (has auth and performance issues now)
- move your API to another web server/instance
- change `client.proxy` in config to `ApiResourceProxyInterface::EXTERNAL`
- set `client.host` to URL of the new API server
- PROFIT!


Installation
------------

- Copy config to `application/config` and edit it if needed.

- Create ApiResource classes named like `\App\Api\Resource\CategoryApiResource`

```php
<?php
namespace \App\Api\Resource;

class CategoryApiResource extends \Spotman\Api\AbstractApiResource
{
    // You may create proxy methods here
    // if you need to call API methods programmatically
}
```

- Create API method classes for these collections named like `\App\Api\Method\Category\AllApiMethod`, each method must return instance of `ApiMethodResponse`; you may use `$this->response()` helper.

```php
<?php
namespace \App\Api\Method\Category;

use \Spotman\Api\ApiMethodResponse;

class AllApiMethod extends \Spotman\Api\Method\AbstractApiMethod
{
    /**
     * @return \Spotman\Api\ApiMethodResponse
     * @throws \Kohana_Exception
     */
  public function execute(): ?ApiMethodResponse {
      $categories = $this->model()->find_all();
      return $this->response($categories);
  }
  
  protected function model($id = NULL): \ORM
  {
      return ORM::factory('Category', $id);
  }
}
```

- Configure frontend for JSON RPC 2.0 API (by default it would be the link [/api/v1/json-rpc](/api/v1/json-rpc))


Example
---

```php
<?php
namespace App;

// For better code quality and IDE autocomplete I recommend writing some helpers in ApiResource classes and in the `application/classes/Application/ApiFacade.php`.
// Now you may write app code using your helpers:

class ApiFacade extends \Spotman\Api\ApiFacade {

    /**
     * @return \App\Api\Resource\CategoryApiResource
     */
    public function category()
    {
        return $this->get('Category');
    }
}

// Instantiate your ApiFacade class via Dependency Injection

class MyService
{
    /**
     * @var \App\ApiFacade
     */
    private $api;
    
    public function __construct(\App\ApiFacade $api)
    {
        $this->api = $api;
    }
    
    public function execute()
    {
        // API call returns instance of ApiMethodResponse
        $categoriesResponse = $this->api->category()->all();

        // Getting model response data
        $categories = $categoriesResponse->getData();

        // And last modified timestamp (if set)
        $categoriesLastModified = $categoriesResponse->getApiLastModified();
        
        return $categories;
    }
}

```

NOTICE
------

**This module is in beta version so everything may be changed later.**

Currently API do not provide auth implementation (I'm searching for a better way).
But in `internal` mode API models methods are executed in context of your app, so you may use `Auth::instance()->get_user()` to get current user or to get other current application state if needed.

TODO:
-----

1. Introduce API versions
2. Full support of the ExternalApiResourceProxy (add auth and remove performance leaks)
3. Response caching
4. ... (your vision would be greatly appreciated)

LICENSE
-------

[MIT License](LICENSE)
