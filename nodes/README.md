Nodes
=======
Nodes are the mechanism for publishing services. A node represents a web endpoint that maps web requests to one or more services.

Simply, a node is a router script/application that sets up the RPC Server handler & class association and triggers the handling of the request.

node-test
=====
Used primarily for integration test, this node starts php built in web server and maps the request URI directly to the rpc server.


node-Zf2
=====
Todo:Planned, a Zf2 application based node, with more advanced configuration and routing available

node-?
=====
Other types of nodes may be officially provided when they are created. You should be able to create a node in any MVC (or non MVC for that matter) framework. So I imagine there may eventually be nodes for CakePHP, Symphony, etc

