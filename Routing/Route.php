<?php
namespace Nest\Routing;

use Exception;

class Route
{
   private $URI = [];
   private $getMethods = [];

   private $postURI = [];
   private $postMethods = [];

   private $RouteNotFoundCode = null;

   /**
    * 
    */
   public function get(string $path, array $methods) {
      $this->URI[] = $path;
      $this->getMethods[] = $methods;
   }

   public function post(string $path, array $methods) {
      $this->postURI[] = $path;
      $this->postMethods[] = $methods;
   }

   private function MethodGET(string $requestedURI) {
      foreach ($this->URI as $key => $path) {
         if ($path == $requestedURI) {
            try {
               $method = new $this->getMethods[$key][0]() or $this->ThrowException("Error Processing Request", 1);               ;

               if (method_exists($method, $this->getMethods[$key][1])) {
                  $func = $this->getMethods[$key][1];
                  $method->$func();
               } else {
                  $fcerror = $this->Error("Function Not Found", '1.75rem');
                  echo "<script>document.body.innerHTML = \"\"</script>";
                  echo $fcerror;
               }

               if (get_class($method) == "Nest\Routing\Route")  {
                  $error = $this->Error("Could not create a object of Route class.", '1rem');
                  
               }

            } catch (Exception $e) {
               $error = preg_replace('/#.*/m', '', $e);
               $error = preg_replace('/Stack trace:/m', '', $error);
               $error = preg_replace('/Exception:/m', '', $error);

               die($this->Error($error, '1rem'));
            }
         } else {
            $error404 = $this->RouteNotFoundCode ?? $this->Error("404 Page Not Found", '2rem');
            die("<script>document.body.innerHTML = '$error404'</script>");
         }
      }
   }

   private function ThrowException(string $error, string $number) {
      throw new Exception($error, $number);
   }

   private function Error(string $error, string $ft) {
      return "<section style=\"width: 100vw; font-family: 'Fira Code'; display: flex; justify-content: center; align-items: center; height: 100vh;\"><div style=\"width: 75%; border-radius: 12.5px; height: 30%; background: #2c3e50; color: #fff; margin-top: -125px; display: flex; justify-content: center; align-items: center; font-size: $ft\">$error</div></section>";
   }

   private function MethodPOST(string $requestedURI) {
      foreach ($this->postURI as $key => $path) {
         if ($path == $requestedURI) {
            try {
               $method = new $this->postMethods[$key][0]() or $this->ThrowException("Error Processing Request", 1);               
            } catch (Exception $e) {
               $error = preg_replace('/#.*/m', '', $e);
               $error = preg_replace('/Stack trace:/m', '', $error);
               $error = preg_replace('/Exception:/m', '', $error);

               die($this->Error($error, '1rem'));
            }

            $method = new $this->postMethods[$key][0]() or $this->ThrowException("Error Processing Request", 1);

            if (get_class($method) == "Nest\Routing\Route")  {
               die($this->Error("Could not create a object of Route class.", '1rem'));
            }

            if (method_exists($method, $this->postMethods[$key][1])) {
               call_user_func($method, $this->postMethods[$key][1]);
            } else {
               die($this->Error("Function Not found.", 1));
            }
         } else {
            $error404 = $this->RouteNotFoundCode ?? $this->Error("404 Page Not Found", '2rem');
            die("<script>document.body.innerHTML = '$error404'</script>");
         }
      }
   }

   public function RouteNotFound(string $customCode) {
      $this->RouteNotFoundCode = $customCode;
   }

   public function Register() {
      $requestedURI = preg_replace('/(.*)\?.*/mi', '$1', $_SERVER['REQUEST_URI']);
      $requestedMethod = 'Method' . $_SERVER['REQUEST_METHOD'];

      $this->$requestedMethod($requestedURI);
   }
}