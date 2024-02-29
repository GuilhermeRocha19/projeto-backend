<?php

namespace Contatoseguro\TesteBackend\Service;

use Contatoseguro\TesteBackend\Config\DB;

class ProductService
{
    private \PDO $pdo;
    public function __construct()
    {
        $this->pdo = DB::connect();
    }

    public function getAll($adminUserId, $active = null, $category = null, $order = 'ASC')
    {
        $query = "
        SELECT p.*, c.title as category
        FROM product p
        INNER JOIN product_category pc ON pc.product_id = p.id
        INNER JOIN category c ON c.id = pc.cat_id
        INNER JOIN product_log l ON (l.product_id = p.id AND l.action = 'create')
        WHERE p.company_id = {$adminUserId}
        GROUP by p.id
        ";
        if ($active !== null) {
            $active = is_numeric($active) ? $active : 1;
            $query .= "AND p.active = '{$active}'";
        }
        if ($category !== null) {
            if (is_numeric($category)) {    
                $query .= "AND c.id = '{$category}'";
            } else {
                $query .= "AND c.title = '{$category}'";
            }
        }
        $order = ($order != 'ASC')?'DESC':'ASC';
        $query .= "ORDER BY l.timestamp $order";

        $stm = $this->pdo->prepare($query);

        $stm->execute();

        return $stm;
    }

    public function getOne($id)
    {
        $stm = $this->pdo->prepare("
        SELECT p.*, c.title as category
        FROM product as p
        INNER JOIN product_category pc ON pc.product_id = p.id
        INNER JOIN category c ON c.id = pc.cat_id
        WHERE p.id  = {$id}
        ");
        $stm->execute();

        return $stm;
    }

    public function insertOne($body, $adminUserId)
    {
        $stm = $this->pdo->prepare("
            INSERT INTO product (
                company_id,
                title,
                price,
                active
            ) VALUES (
                {$body['company_id']},
                '{$body['title']}',
                {$body['price']},
                {$body['active']}
            )
        ");
        if (!$stm->execute())
            return false;

        $productId = $this->pdo->lastInsertId();

        $stm = $this->pdo->prepare("
            INSERT INTO product_category (
                product_id,
                cat_id
            ) VALUES (
                {$productId},
                {$body['category_id']}
            );
        ");
        if (!$stm->execute())
            return false;

        $stm = $this->pdo->prepare("
            INSERT INTO product_log (
                product_id,
                admin_user_id,
                `action`
            ) VALUES (
                {$productId},
                {$adminUserId},
                'create'
            )
        ");

        return $stm->execute();
    }

    public function updateOne($id, $body, $adminUserId)
    {
        $stm = $this->pdo->prepare("
            UPDATE product
            SET company_id = {$body['company_id']},
                title = '{$body['title']}',
                price = {$body['price']},
                active = {$body['active']}
            WHERE id = {$id}
        ");
        if (!$stm->execute())
            return false;

        $stm = $this->pdo->prepare("
            UPDATE product_category
            SET cat_id = {$body['category_id']}
            WHERE product_id = {$id}
        ");
        if (!$stm->execute())
            return false;

        $stm = $this->pdo->prepare("
            INSERT INTO product_log (
                product_id,
                admin_user_id,
                `action`
            ) VALUES (
                {$id},
                {$adminUserId},
                'update'
            )
        ");

        return $stm->execute();
    }

    public function deleteOne($id, $adminUserId)
    {
        $stm = $this->pdo->prepare("
            DELETE FROM product_category WHERE product_id = {$id}
        ");
        if (!$stm->execute())
            return false;

        $stm = $this->pdo->prepare("DELETE FROM product WHERE id = {$id}");
        if (!$stm->execute())
            return false;

        $stm = $this->pdo->prepare("
            INSERT INTO product_log (
                product_id,
                admin_user_id,
                `action`
            ) VALUES (
                {$id},
                {$adminUserId},
                'delete'
            )
        ");

        return $stm->execute();
    }

    public function getLog($adminUserId, $id = null)
    {
        $sql = "SELECT l.admin_user_id, a.name, p.title as product, l.action, l.timestamp
        FROM product_log l
        INNER JOIN product p ON p.id = l.product_id
        INNER JOIN admin_user a ON a.id = l.admin_user_id
        WHERE l.admin_user_id = '{$adminUserId}'";

        if ($id !== null) {
            $sql .= " AND l.product_id = '{$id}'";
        }

        $stm = $this->pdo->prepare($sql);
        $stm->execute();


        return $stm;
    }
}
