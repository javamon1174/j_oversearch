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

class Frequency extends ORM
{
    private $user_idx;

    function InsertFrequency2Table
                                (
                                    $user_idx = 0,
                                    $data = Array(),
                                    $count = 0
                                )
    {
        $this->user_idx = $user_idx;
        $this->db_connect->beginTransaction();

        try {
            $query = $this->InsertFrequencyQuery($count); //get query for insert
            $prepared_query = $this->db_connect->prepare($query);
            $prepared_query->execute($data);
            return $this->db_connect->commit();
        } catch (Exception $e) {
            $this->db_connect->rollBack();
            return false;
        }
    }

    public function select
                            (
                                $select = '*',
                                $where_colmn = null,
                                $where_value = null
                             )
    {
        try {
            $this->db_connect->beginTransaction();

            $this->abort_error(empty($where_colmn || $where_value), "No arguments were passed to select it.");

            $sql = "SELECT {$select} FROM ".static::$table." WHERE `".static::$table."`.`{$where_colmn}`='{$where_value}' ORDER BY `".static::$table."`.`freq_index` ASC;";

            $prepared_sql = $this->db_connect->prepare($sql);
            $prepared_sql->execute() ? $prepared_sql : $this->abort_error(true , "SELECT ERORROR - Check Query");
            $this->db_connect->commit();

            return $prepared_sql;

        } catch (PDOException $e) {
            $this->db_connect->rollBack();
            return false;
        }
    }

    private function InsertFrequencyQuery($hero_time_data_count = 0)
    {
        $query = "INSERT INTO `".static::$table."` (`user_idx`, `hero`, `freq_index`, `time`,  `win`, `outcome`, `accuracy`, `K/D`, `simul_kill`, `con_kill`)  VALUES ";
        $query_value = "";
        for ($i=0; $i < $hero_time_data_count ; $i++)
        {
            $query_value = $query_value."('".$this->user_idx."', ?, ?, ?, ?, ?, ?, ?, ?, ?),";
        }
        $query_value = substr($query_value, 0, -1); $query_value = $query_value.";";
        return $query.$query_value;
    }
}