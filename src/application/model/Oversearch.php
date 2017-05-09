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

use Javamon\Jframe\Core\Model as Model;

class Oversearch extends Model
{
    public function deleteAllData($user_idx = 0)
    {
         try {
             $sql =
             "DELETE FROM summarys, frequencys, heroes, times
              USING summarys, frequencys, heroes, times
              WHERE summarys.user_idx=frequencys.user_idx
              AND frequencys.user_idx=heroes.user_idx
              AND heroes.user_idx=times.user_idx
              AND times.user_idx={$user_idx};"
              ;

             $this->db_connect->beginTransaction();

             $prepared_sql = $this->db_connect->prepare($sql);
             $prepared_sql->execute() ? $prepared_sql : $this->abort_error(true , "Query ERROR");
             return $this->db_connect->commit();

         } catch (PDOException $e) {
             $this->db_connect->rollBack();
             return false;
         }

    }

    // public function selectAllData()
    // {
    //     /* SELECT *
    //         FROM summarys s, times t, frequencys f
    //         WHERE f.user_idx=s.user_idx
    //         AND s.user_idx=t.user_idx
    //         AND t.user_idx=1 */
    //
    //         /* SELECT * FROM summarys AS s RIGHT OUTER JOIN frequencys AS f
    //         ON  s.user_idx = f.user_idx
    //         AND f.user_idx = 1
    //         WHERE s.user_idx = 1 */
    // }
}
