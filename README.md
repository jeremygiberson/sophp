SOPHP
=====

Service Oriented PHP

Redefinition
====
For clarity, lets use these definitions. They may not be right, but lets use them anyway.

  - Service: A service is simply a PHP class that has been registered in the Service Cloud. (not all classes are services)
  - Webservice: A service that has been exposed via a network endpoint. (example.com/api/customer exposes Customer service)
  - Service Cloud: Registry of "Services", shared by one or more projects of an organization. 

Sample Case
====
Frequently when talking about SOA the immediate instinct is to "Webservice"-ify several of the domain components. So that the persistence & business logic is located in a central project and other projects can make use of that logic by calling out to it. For example, lets say we want to add a contact to our application. 

We might implement such a task with the following frontend and backend code:
    <?php
    // somewhere in the application mvc, we process form data
    function addContact($data) {
      $company = marshalResponseToCompany(
        companyWebService::getCompany($data->getCompanyId())
      );
      $person = marshalResponseToPerson(
        personWebService::create(
          $data->getPersonName(),
          $data->getPersonAffiliation(),
          ...
        )
      );
      $address = marshalResponseToAddress(
        addressWebService::create(
          $data->getAddress(), $data->getCity(), $data->getState(), ...
        ),
      ),
    }
    
    // then in the server mvc, we process the crud requests (repeat for each service)
    function create() {
      validateRequestData();
      $params = marshalRequestDataToParameters();
      $model = <address,person,company,etc>service::create($params);
      return marshalModelTo<json,xml>();
    }

There are a couple of problems here that tend to snowball in a growing code base (SOA or not)
  * Everything is a Webservice resulting in lots of network traffic. Should you really have to accross the network to do some read only work? Shouldn't you be able to do that work in place if possible?
  * On top of writing the service for persistence, you had to write additional Webservice and Application code
  * Writing a bunch of marshallers to handle the passing of data over the wire
  * Maintaining the endpoint information for the Webservice implementation (company=/company,person=/person,etc)
  * Violating DRY. You write a service for the storage, then you write another service to wrap the calling of the business service over the wire. You write two services with a single net result. 
  * You have to be mindful about where the code is running. Are you writing code on the backend where you can work with the service directly? Or are you on the frontend/different project where you have to access the Webservice in its place.

Design Philosophy
====
So obviously the goal of SOPHP is to address the above issues. 

Service Oriented means: You should be able to
  * write a service once
  * use it in a bunch of disparate projects across your organization
  * utilize the resources and configuration of the calling project to execute a service locally
  * fail back to a remote execution of the service (IE call a Webservice)
  * not know or care which of the two prior operations occurred
  * not maintain endpoint configuration in any projects!

Strategy
====

SOPHP intends to utilize is the proxy and registry pattern at it's core. Instead of instantiating a Service or a Webservice, you ask the registry for a ServiceInterface--which can be satisfied by either a Service or Webservice. The Service Registry will automatically determine the appropriate concrete to satisfy the interface. 

    [ServiceInterface]       [Registry           ]
      ^            ^         |+ get(serviceName) ]
      |            |
    [Service]<---[Proxy]     $service = Registry::get('ServiceInterface'); // Service or Proxy
  
However, instead of actually asking the registry directly for a ServiceInterface SOPHP will provide you the ability to get instances through DependencyInjection or simple class instantiation. 

SOPHP will abstract away the need to ask it for a 'ServiceInterface' so you can instead ask it for a 'Service'. SOPHP does it's thing and you end up with a Service or you end up with a Proxy.

    $service = new Org\My\Component(); // might be a Component, might be a Proxy for Component. 
    $service->doSomething(); // might be doing something locally, or over the wire -- you don't know or care

How does it work? Pretty simply actually, the Registry Cloud provides an spl_autoloader. In your project when you ask for an Org\My\Component the autoloader will attempt to locate the class in your project tree and load it. If it can't find it, it will then check the Registry Cloud to see if such a service exists. If it does, it will generate a proxy automatically and return that to you instead. Despite being a proxy, the object will have the same interface and behave exactly the same--the business logic is simply deferred over the wire to where the Webservice is hosted, all transparently.

Opt-In Cloud Preference
=====
It could also be the case that you actually have the package installed locally but would still prefer to use a proxy to a server instance of the service. For example, a service that requires a database resource that you dont have the configuration information for. In this case, you can configure the autoloader to always use the registry cloud to provide a proxied service for all services that require that resource. So, even if you have the package providing the service installed in the project directory you'll still get a proxy version of the service.


