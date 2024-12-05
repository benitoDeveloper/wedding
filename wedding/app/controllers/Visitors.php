<?php

class Visitors extends Controller 
{
      protected $visitor_model;
      protected $menu_model;
      public function __construct()
      {
            $this->visitor_model = $this->model('visitor');
            $this->menu_model = $this->model('menu');

      }
      public function index() 
      {
            if($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST)) redirect('pages');
            $data = sanitise($_POST);
            set_session($data);

            if($this->visitor_model->is_registered($data['email']))
            {
                  set_session(null, $this->visitor_model->get_visitor_id($data['email']));

                  redirect('visitors/existing');
                  return;
            }
            if($this->visitor_model->register_visitor($data) === true)
            {
                  if($data['attendance'] === 'not_attend')
                  {
                        $this->view('visitors/not_attend', $data); 
                        return;
                  }
                  redirect('visitors/menu');
            }
      }
      public function menu()
      {
            if(empty($_SESSION)) redirect('pages');

            $visitor_has_menu = $this->menu_model->has_menu($_SESSION['visitor_id']);

            if($visitor_has_menu) 
            {
                  $this->view('visitors/has_menu', $_SESSION);
            } 
            else
            {

                  $this->view('visitors/menu',$_SESSION);
            }
      }
      public function register_menu($menu_confirmed=false)
      {
            if($this->menu_model->has_menu($_SESSION['visitor_id'])) $this->view('visitors/has_menu', $_SESSION);

            if($_SERVER['REQUEST_METHOD'] === "POST" && !empty($_POST))
            {
                  set_session(sanitise($_POST));
                  $data = $_SESSION;
                  $this->view('visitors/menu_confirmation', $data);
                  return;
            } 
            if($_SERVER['REQUEST_METHOD'] === "GET" && !empty($_GET))
            {
                  if($menu_confirmed)
                  {
                        $data = $_SESSION;
 
                        if($this->visitor_model->register_menu($data)=== true)
                        {
                              $this->view('visitors/menu_submitted', $data);
                              return;
                        }
                        die("something went wrong");
                  }
            }
      }
      public function edit_attendance($status = false)
      {
            if(!isset($_SESSION)) session_start();
            $data = $_SESSION;
            $this->view('visitors/edit_attendance',$data);

      }
      public function edit_process() 
      {
            if(!isset($_SESSION)) session_start();
            $data = $_SESSION;

            if(!$this->visitor_model->is_registered($data['email'])) redirect('pages');
            set_session(sanitise($_POST));
            $_SESSION['visitor_id'] = $this->visitor_model->get_visitor_id($data['email']);

            if($this->visitor_model->edit_visitor($data))
            {
                  flash_msg("visitor_msg", "your details have been updated", "success");
                  redirect('visitors/existing', $_SESSION);
            }
            else 
            {
                  flash_msg("visitor_msg", "something went wrong: your record was not updated", "danger");
                  redirect('visitors/existing', $_SESSION);
            }
      }
      public function existing()
      {
            $data = $_SESSION;
            $this->view('visitors/existing',$data);
      }
}
?>