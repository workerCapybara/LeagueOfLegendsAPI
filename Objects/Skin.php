<?php

class Skin{
    private $name;
    private $price;
    private $champion_id;
    public $skin_id;

    public function setValues($name, $price, $champion_id){
        $this->name = $name;
        $this->price = $price;
        $this->champion_id = $champion_id;
        $this->skin_id= null;
    }

    public function getName(){
        return $this->name;
    }

    public function getPrice(){
        return $this->price;
    }

    public function getChampionId(){
        return $this->champion_id;
    }

    public function setPrice($price){
        $this->price = $price;
    }

    public function setChampionId($champion_id){
        $this->champion_id = $champion_id;
    }

    public function setSkinId($skin_id){
        $this->skin_id = $skin_id;
    }
    

    public function setNombre($name){
        $this->name = $name;
    }

}
?>