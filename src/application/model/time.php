<?php
/**
 * Javamon's JFramework
 *
 * PHP 컴포저 기반 제이프레임워크
 *
 * Created on 2017. 5.
 * @package      Javamon\Jframe
 * @category     Index
 * @license      http://opensource.org/licenses/MIT
 * @author       javamon <javamon1174@gmail.com>
 * @link         http://javamon.be/Jframe
 * @link         https://github.com/javamon1174/jframe
 * @version      0.0.1
 */
namespace Javamon\Jframe\Model;

use \Javamon\Jframe\Core\ORM as ORM;

class Time extends ORM
{
    private $user_idx;

    function InsertTime2Table
                                (
                                    $data = Array()
                                )
    {
        $this->db_connect->beginTransaction();

        try {
            $query = $this->InsertTimeQuery(); //get query for insert
            $prepared_query = $this->db_connect->prepare($query);
            $prepared_query->execute($data);
            return $this->db_connect->commit();
        } catch (Exception $e) {
            $this->db_connect->rollBack();
            return false;
        }
    }

    private function InsertTimeQuery()
    {
        //SELECT t.update_date FROM `summary` s, `time` t  WHERE t.user_idx = (s.`user_name` LIKE '희열-31851');
        $query = "INSERT INTO `".static::$table."` (`user_idx`, `update_date`) VALUES (:user_idx, :update_date);";
        return $query;
    }
}