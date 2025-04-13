<?php
require_once 'app/Controllers/api.controller.php';
require_once 'app/Models/skins.model.php';
require_once 'Objects/Skin.php';
require_once 'app/Helpers/auth.api.helper.php';

class ApiSkins extends ApiController{
    private $model;
    private $AuthHelper;

    function __construct() {
        parent::__construct();
        $this->model = new Skins_model();
        $this->AuthHelper=new AuthHelper();
    }

    public function getById($params = []) {
        $id = $params[":Skin_id"];
        $champion = $this->model->getSkinsById($id);
        if (!empty($champion)) {
            $this->view->response([
                'data' => $champion,
                'status' => 'success',
            ], 200);
        } else {
            $this->view->response([
                'data' => 'La Skin solicitada no existe',
                'status' => 'error'
            ], 404);
        }
  


    }
   public function get($params = []) {
    
        $columns = $this->model->getColumnNames();
        

        //Array where consult parameters are stored
        $queryParams = array();

        //Filter
        $queryParams += $this->handleFilter($columns);
        
        //Sort
        $queryParams += $this->handleSort($columns);

        //Pagination
        $queryParams += $this->handlePagination();
        
        //Se obtienen los álbumes y se devuelven en formato JSON
        $Skins = $this->model->getAllSkins($queryParams);
        return $this->view->response($Skins, 200);
    }
    
    


    function delete($params = []) {
            $Skin_id = $params[':Skin_id'];
            $Skins = $this->model->getSkinsById($Skin_id);

            if($Skins) {
                $this->model->deleteSkins($Skin_id);
                $this->view->response('La Skin con id='.$Skin_id.' ha sido borrada.', 200);
            } else {
                $this->view->response('La Skin con id='.$Skin_id.' no existe.', 404);
            }
        }

        function createSkins($params = []) {

            $user = $this->AuthHelper->currentUser();   //Verifico que el usuario este logueado
            if (!$user) {
                $this->view->response('El usuario no esta autorizado para realizar esta accion', 401);
                return;
            }

            $data = $this->getData();
    
            if (empty($data->name) || empty($data->champion_id) || empty($data->price)) {
                $this->view->response([
                    'data' => 'There is a missing field',
                    'status' => 'error'
                ], 400);
            }else{
            $skin = new Skin();
            $skin->setValues($data->name, $data->price, $data->champion_id);
    
            $Skin_id = $this->model->insertSkins($skin->getChampionId(), $skin->getName() ,$skin->getPrice()); //insertSkins returns last skin inserted id
            $Skin_added = $this->model->getSkinsById($Skin_id);                                                
    
            if ($Skin_added) {
                $this->view->response([
                    'data' => $Skin_added,
                    'status' => 'success'
                ], 200);
            } else
                $this->view->response([
                    'data' => "Skin was not created",
                    'status' => 'error'
                ], 500);
            }
        }

        function update($params = []) {

            $user = $this->AuthHelper->currentUser();   //Verifico que el usuario este logueado
            if (!$user) {
                $this->view->response('User is unauthorized to do this action', 401);
                return;
            }

            $Skin_id = $params[':Skin_id'];
            $skin = $this->model->getSkinsById($Skin_id);

            if($skin) {
                $body = $this->getData();
                $name = $body->name;
                $price = $body->price;
                $this->model->updateSkins($Skin_id, $name, $price);

                $this->view->response('Skin with id='.$Skin_id.' has been modified.', 200);
            } else {
                $this->view->response('Skin with id='.$Skin_id.' does not exist.', 404);
            }
        }

        private function handleFilter($columns) {
            //Default values
            $filterData = [
                'filter' => "", //Filter field
                'value' => ""   //Value field
            ];
    
            if (!empty($_GET['filter']) && !empty($_GET['value'])) {
                $filter = $_GET['filter'];
                $value = $_GET['value'];

          var_dump($filter);
          var_dump($value);
    
                //If the fiel does not exist it produces an error
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
            // Valores por defecto
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