<?php
require_once 'app/Views/api.view.php';

abstract class ApiController {
    protected $view;
    private $data;

    function __construct() {
        $this->view = new ApiView();
        $this->data = file_get_contents('php://input'); //Reads the text
    }

    function getData() {
        return json_decode($this->data);    //Converts the text into JSON format
    }
}