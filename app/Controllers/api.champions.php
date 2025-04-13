<?php
require_once 'app/Controllers/api.controller.php';
require_once 'app/Models/champions.model.php';
require_once 'Objects/Champion.php';
require_once 'app/Helpers/auth.api.helper.php';

class ApiChampions extends ApiController {
    private $model;
    private $AuthHelper;

    function __construct() {
        parent::__construct();
        $this->model = new Champions_model();
        $this->AuthHelper=new AuthHelper();
    }

    public function getById($params = []) {
        $id = $params[":Champion_id"];
        $champion = $this->model->getChampionById($id);
        if (!empty($champion)) {
            $this->view->response([
                'data' => $champion,
                'status' => 'success',
            ], 200);
        } else {
            $this->view->response([
                'data' => 'Required champion does not exist',
                'status' => 'error'
            ], 404);
        }
    }


    function get($params = []) {

        $columns = $this->model->getColumnNames();

        //Array where consult parameters are stored
        $queryParams = array();

        //Filter
        $queryParams += $this->handleFilter($columns);
        
        //Sort
        $queryParams += $this->handleSort($columns);

        //Pagination
        $queryParams += $this->handlePagination();
        
        //Obtains the skins and handles them in JSON format
        $champions = $this->model->getAllChampions($queryParams);
        return $this->view->response($champions, 200);
    }

    function delete($params = []) {

        $user = $this->AuthHelper->currentUser();   //Verify that the user is logged
        if (!$user) {
            $this->view->response('User is unauthorized to do this action', 401);
            return;
        }
        
            $Champion_id = $params[':Champion_id'];
            $campeon = $this->model->getChampionById($Champion_id);

            if($campeon) {
                $this->model->deleteChampion($Champion_id);
                $this->view->response('El campeon con id='.$Champion_id.' ha sido borrada.', 200);
            } else {
                $this->view->response('El campeon con id='.$Champion_id.' no existe.', 404);
            }
        }


     function createChampion($params = []) {

        $user = $this->AuthHelper->currentUser();   //Verify that the user is logged
        if (!$user) {
            $this->view->response('User is unauthorized to do this action', 401);
            return;
        }

        $data = $this->getData();
        if (empty($data->name) || empty($data->price) || empty($data->role)) {    //Verify that asked fields are not ampty
            $this->view->response([
                'data' => 'There is some field missing',
                'status' => 'error'
            ], 400);
        }else{
        $champion = new Champion();
        $champion->setValues($data->name, $data->role, $data->price);

        $Champion_id = $this->model->insertChampion($champion->getName(),$champion->getRole(),$champion->getprice()); //insertChampion devuelve el id del
        $Champion_added = $this->model->getChampionById($Champion_id);                                      //ultimo campeon insertado

        if ($Champion_added) {
            $this->view->response([
                'data' => $Champion_added,
                'status' => 'success'
            ], 200);
        } else
            $this->view->response([
                'data' => "Champion was not created",
                'status' => 'error'
            ], 500);
    
        }
    }

        function update($params = []) {

                $user = $this->AuthHelper->currentUser();   //Verify that the user is logged
            if (!$user) {
                $this->view->response('User is not authorized to do this action', 401);
                return;
            }
            $Champion_id = $params[':Champion_id'];
            $champion = $this->model->getChampionById($Champion_id);

            if($champion) {
                $body = $this->getData();
                $name = $body->name;
                $role = $body->role;
                $price = $body->price;
                $this->model->updateChampion($name, $role, $price, $Champion_id);

                $this->view->response('Champion with id='.$Champion_id.' has been modified.', 200);
            } else {
                $this->view->response('Champion with id='.$Champion_id.' does not exist.', 404);
            }
        }

        private function handleFilter($columns) {
            // Valores por defecto
            $filterData = [
                'filter' => "", // Campo de filtrado
                'value' => ""   // Valor de filtrado
            ];
    
            if (!empty($_GET['filter']) && !empty($_GET['value'])) {
                $filter = $_GET['filter'];
                $value = $_GET['value'];

          
    
                //If the field does not exist it produces an error
                if (!in_array($filter, $columns)) {
                    $this->view->response("Invalid filter parameter (field '$filter' does not exist)", 400);
                    die();
                }
               
                $filterData['filter'] = $filter;
                $filterData['value'] = $value;
           
            }
            
            return $filterData;
        }
    

        //Sort method using sort and order fields
        private function handleSort($columns) {
            //Default values
            $sortData = [
                'sort' => "", //Sort field
                'order' => "" //Upward or downward order
            ];
    
            if (!empty($_GET['sort'])) {
                $sort = $_GET['sort'];

              
    
                //If sort field does not exist it produces an error
                if (!in_array($sort, $columns)) {
                    $this->view->response("Invalid sort parameter (field '$sort' does not exist)", 400);
                    die();
                }
    
                //Upward or downward order
                if (!empty($_GET['order'])) {
                    $order = strtoupper($_GET['order']);
                    $allowedOrders = ['ASC', 'DESC'];
    
                    //If order field does not exist it produces an error
                    if (!in_array($order, $allowedOrders)) {
                        $this->view->response("Invalid order parameter (only 'ASC' or 'DESC' allowed)", 400);
                        die();
                    }
                }
    
                $sortData['sort'] = $sort;
                $sortData['order'] = $order;
            }
    
            return $sortData;
        }
    

        //Pagination method using page number and limits
        private function handlePagination() {
            //Default values
            $paginationData = [
                'limit' => 0,    //Results limit
                'offset' => 0    //Displacement
            ];
  
    
            if (!empty($_GET['page']) && !empty($_GET['limit'])) {
                $page = $_GET['page'];
                $limit = $_GET['limit'];

                //If some of the values is not a natural number it produces an error
                if (!is_numeric($page) || $page < 0 || !is_numeric($limit) || $limit < 0) {
                    $this->view->response("Page and limit parameters must be positive integers", 400);
                    die();
                }
    
                $offset = ($page - 1) * $limit;
    
                $paginationData['limit'] = $limit;
                $paginationData['offset'] = $offset;
            }
           
            return $paginationData;
        }
    
    

    }