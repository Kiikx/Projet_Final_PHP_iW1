<?php

class Router
{
  private array $routes;

  public function __construct()
  {
    $this->routes = [];
  }

  public function get(string $path, string $controllerName, string $methodName): void
  {
    $this->routes[] = [
      "method" => "GET",
      "path" => $path,
      "controllerName" => $controllerName,
      "methodName" => $methodName
    ];
  }

  public function post(string $path, string $controllerName, string $methodName): void
  {
    $this->routes[] = [
      "method" => "POST",
      "path" => $path,
      "controllerName" => $controllerName,
      "methodName" => $methodName
    ];
  }

  public function start(): void
  {
      $method = $_SERVER["REQUEST_METHOD"];
      $fullUrl = $_SERVER["REQUEST_URI"];
      
      
      $urlParts = parse_url($fullUrl);
      $path = $urlParts["path"]; 
      parse_str($urlParts["query"] ?? "", $_GET); 
  
      foreach ($this->routes as $route) {
          if ($method === $route["method"]) {
              $routePath = $route["path"];
              $regex = $this->convertPathToRegex($routePath);
  
              if (preg_match($regex, $path, $matches)) {
                  array_shift($matches);
  
                  $controllerName = $route["controllerName"];
                  $methodName = $route["methodName"];
  
                  if (!empty($matches)) {
                      call_user_func_array([$controllerName, $methodName], $matches);
                  } else {
                      $controllerName::$methodName();
                  }
                  return;
              }
          }
      }
  
      // Si aucune route ne correspond, afficher une 404
      http_response_code(404);
      echo "404 Not Found";
  }
  

  private function convertPathToRegex(string $path): string
  {
      // Convert route parameters (e.g., {id}) into regex capture groups
      $pattern = preg_replace('/\{([^}]+)\}/', '(?P<$1>[^/]+)', $path);
      return "#^" . $pattern . "$#";
  }
}
