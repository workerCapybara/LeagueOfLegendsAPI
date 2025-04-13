<?php
require_once 'app/Models/model.php';

class Skins_model extends Model {
    
    public   function getAllSkins( $queryParams) {
        $sql = "SELECT * FROM skins";

        //Filter
        if (!empty($queryParams['filter']) && !empty($queryParams['value']))
            $sql .= ' WHERE ' . $queryParams['filter'] . ' LIKE \'%' . $queryParams['value'] . '%\'';

        //Sort
        if (!empty($queryParams['sort'])) {
            $sql .= ' ORDER BY '. $queryParams['sort'];

            //Upward and downward order
            if (!empty($queryParams['order']))
                $sql .= ' ' . $queryParams['order'];}

                if (!empty($queryParams['limit']))
                $sql .= ' LIMIT ' . $queryParams['limit'] . ' OFFSET ' . $queryParams['offset'];
    
            //There is no need to sanitize the query (submitted data already verified by controller)
            $query = $this->db->prepare($sql);        
            $query->execute();
    
            $skin = $query->fetchAll(PDO::FETCH_OBJ);
            return $skin;
    }

    public function getColumnNames() {
        $query = $this->db->query('DESCRIBE skins');
        $columns = $query->fetchAll(PDO::FETCH_COLUMN);
        return $columns;
    }

    public   function getSkinsById($Skin_id) {
        $query = $this->db->prepare('SELECT *, skins.Name AS SkinName FROM skins JOIN champions ON champions.Champion_id = skins.Champion_id WHERE skin_id = ?');
        $query->execute([$Skin_id]);


        $Skins = $query->fetchAll(PDO::FETCH_OBJ);

        return $Skins;
    }

    public  function insertSkins($Champion_id, $name, $price) {
        $query = $this->db->prepare('INSERT INTO skins (Champion_id, Name, Price) VALUES(?,?,?)');
        $query->execute([$Champion_id, $name, $price]);

        return $this->db->lastInsertId();
    }


    public function deleteSkins($Skin_id) {
        $query = $this->db->prepare('DELETE FROM Skins WHERE Skin_id = ?');
        $query->execute([$Skin_id]);
    }

    public function updateSkins($Skin_id, $name, $Price) {
        $query = $this->db->prepare('UPDATE skins SET Name = ?, Price = ? WHERE Skin_id = ?');
        $query->execute([$name, $Price, $Skin_id]);
    }
}