<?php

class Core {
      protected $current_controller = 'Pages';
      protected $current_method = 'index';
      protected $params;

      public function __construct()
      {
            $url = $this->get_url();
            // var_dump($url);

            if(file_exists(APPROOT . '/' .'controllers/' . $url[0] . '.php')) {
                  $this->current_controller = ucwords($url[0]);
                  unset($url[0]);
            }
            require_once(APPROOT . '/' .'controllers/' . $this->current_controller . '.php');
            $this->current_controller = new $this->current_controller;
            if(isset($url[1]) && method_exists($this->current_controller, $url[1])) {
                  $this->current_method = $url[1];
                  unset($url[1]);
            }
            $this->params = $url? array_values($url) : [];
            call_user_func_array([$this->current_controller,$this->current_method], $this->params);
   
      }
      protected function get_url() {
            $url = isset($_GET['url']) ? trim($_GET['url']) : "";
            // $url = ltrim($url,'/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            // var_dump($url);
            return $url;
      }
}
?>