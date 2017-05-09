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

class Summary extends ORM
{
    private $user_idx;

    function getInsertUserIdx()
    {
        try {
            $sql = "SELECT user_idx FROM ".static::$table." ORDER BY `user_idx` DESC LIMIT 1;";

            $this->db_connect->beginTransaction();

            $prepared_sql = $this->db_connect->prepare($sql);
            $prepared_sql->execute() ? $prepared_sql : $this->abort_error(true , "Query ERROR");
            $this->db_connect->commit();

            return $prepared_sql->fetch(\PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->db_connect->rollBack();
            return false;
        }
    }

    function InsertSummary2Table
                                (
                                    $user_idx = 0,
                                    $data = Array()
                                )
    {
        $this->user_idx = $user_idx;
        $this->db_connect->beginTransaction();

        try {
            $query = $this->InsertSummaryQuery(); //get query for insert
            $prepared_query = $this->db_connect->prepare($query);
            $prepared_query->execute($data);
            return $this->db_connect->commit();
        } catch (Exception $e) {
            $this->db_connect->rollBack();
            return false;
        }
    }

    private function InsertSummaryQuery()
    {
        $query = "INSERT INTO `".static::$table."` (`user_idx`, `icon`, `level`, `com_grade`, `user_name`, `avg_kill`, `avg_damage`, `avg_death`,
                  `avg_Murderous`, `avg_heal`, `avg_contributions_kill`, `avg_contributions_time`, `avg_solo_kill`, `level_img`, `analy`)
                  VALUES ({$this->user_idx}, :icon, :level, :com_grade, :user_name, :avg_kill, :avg_damage, :avg_death, :avg_Murderous, :avg_heal,
                  :avg_contributions_kill, :avg_contributions_time, :avg_solo_kill, :level_img, :analy)";
        return $query;
    }


}