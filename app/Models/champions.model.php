<?php
require_once 'app/Models/model.php';

class Champions_model extends Model {
    public function getAllChampions( $queryParams) {
        $sql = "SELECT * FROM champions";

        //Filter
        if (!empty($queryParams['filter']) && !empty($queryParams['value']))
            $sql .= ' WHERE ' . $queryParams['filter'] . ' LIKE \'%' . $queryParams['value'] . '%\'';

        //Sort
        if (!empty($queryParams['sort'])) {
            $sql .= ' ORDER BY '. $queryParams['sort'];

            //Upward and downward order
            if (!empty($queryParams['order']))
                $sql .= ' ' . $queryParams['order'];
        }

        //Pagination
        if (!empty($queryParams['limit']))
            $sql .= ' LIMIT ' . $queryParams['limit'] . ' OFFSET ' . $queryParams['offset'];

        //There is no need to sanitize the query (submitted data already verified by controller)
        $query = $this->db->prepare($sql);        
        $query->execute();

        $champ = $query->fetchAll(PDO::FETCH_OBJ);
        return $champ;
    }

    public function getColumnNames() {
        $query = $this->db->query('DESCRIBE champions');
        $columns = $query->fetchAll(PDO::FETCH_COLUMN);
        return $columns;
    }

    public function updateChampion($name, $role, $Precio, $Champion_id) {

        $query = $this->db->prepare('UPDATE champions SET name = ?, role = ?, price = ? WHERE Champion_id = ?');
        $query->execute([$name, $role, $Precio, $Champion_id]);
    }

    public function getChampionById($Champion_id) {
        $query = $this->db->prepare('SELECT * FROM `champions` WHERE Champion_id=?');
        $query->execute([$Champion_id]);

        $champ = $query->fetchAll(PDO::FETCH_OBJ);

        return $champ;
    }

    public function insertChampion($name, $role, $price) {
        $query = $this->db->prepare('INSERT INTO champions (name, role,price) VALUES(?,?,?)');
        $query->execute([$name, $role, $price]);

        return $this->db->lastInsertId();
    }

    public function deleteChampion($Champion_id) {
        $query = $this->db->prepare('DELETE FROM champions WHERE Champion_id =?');
        $query->execute([$Champion_id]);
    }

    
}