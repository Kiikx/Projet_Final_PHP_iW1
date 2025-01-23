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
      $path = $_SERVER["REQUEST_URI"];

      foreach ($this->routes as $route) {
          if ($method === $route["method"]) {
              $routePath = $route["path"];
              $regex = $this->convertPathToRegex($routePath);
              if (preg_match($regex, $path, $matches)) {
                  array_shift($matches); // Remove the full match
                  
                  $controllerName = $route["controllerName"];
                  $methodName = $route["methodName"];

                  echo("<pre>");
                  $controllerName::$methodName($matches["slug"]);
                  return;
              }
          }
      }

      // If no route matches, return a 404
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
