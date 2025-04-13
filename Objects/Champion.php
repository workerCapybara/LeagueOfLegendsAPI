<?php

class Champion{

    private $name;
    private $role;
    private $price;
    public $champion_id;

    public function setValues($name, $role, $price){
        $this->name = $name;
        $this->role = $role;
        $this->price = $price;
        $this->champion_id = null;

    }

    public function getName(){
        return $this->name;
    }

    public function getRole(){
        return $this->role;}


    public function getPrice(){
        return $this->price;
    }

    public function setRol($Role){
        $this->rol = $Role;
    }
    public function setPrecio($price){
        $this->precio = $price;
    }

    public function setChampionId($champion_id){
        $this->champion_id = $champion_id;
    }

    public function setNombre($name){
        $this->name = $name;
    }
}
?>