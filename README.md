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

SOPHP utilizes Proxy and Service Locator patterns at it's core. Using the service locator an instance is provided that satisfies the specified interface. Internally the locator determines if the concrete can be satisifed locally. If it cannot, a proxy to a remote concrete is generated. 





