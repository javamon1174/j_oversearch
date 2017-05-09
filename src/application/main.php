<?php
/**
 * Javamon's JFramework
 *
 * PHP 컴포저 기반 제이프레임워크
 *
 * Created on 2017. 5.
 * @package      Javamon\Jframe
 * @category     Main
 * @license      http://opensource.org/licenses/MIT
 * @author       javamon <javamon1174@gmail.com>
 * @link         http://javamon.be/Jframe
 * @link         https://github.com/javamon1174/jframe
 * @version      0.0.1
 */
namespace Javamon\Jframe\Processor;

use \Javamon\Jframe\Core\Processor as Processor;
use \Javamon\Jframe\Core\ORM as ORM;
use \Javamon\Jframe\Model\Oversearch as Oversearch;
use \Javamon\Jframe\Model\Summary as Summary;
use \Javamon\Jframe\Model\Frequency as Frequency;
use \Javamon\Jframe\Model\Heroe as Heroe;
use \Javamon\Jframe\Model\Time as Time;

class Main extends Processor
{
    private $index = 0;
    private $user_idx = 0;
    private $user_id = '';

    public function index($arg)
    {
        $this->constDefineInProcessor($arg);

        $layout[] = "header";
        $layout[] = "index";
        $layout[] = "footer";

        $data["host"] = HTTP_HOST;
        $data["user"] = DE_USERNAME;

        return $this->view->load($layout, $data);
    }

    public function main($arg)
    {
        echo "main page <br />";
        var_dump($arg);
    }

    public function test($arg)
    {
        echo "test page <br />";
        var_dump($arg);
    }

    public function view($arg)
    {
        $this->constDefineInProcessor($arg);
        $result_data = Summary::ORM()->select("user_idx", "user_name", DE_USERNAME);
        $result_data = $result_data->fetch();
        $user_idx = (int) $result_data["user_idx"];

        if ($user_idx == null)
        {
            return header('Location: '.HTTP_HOST."/main/search/user/".DE_USERNAME);
        }

        $summary_data = Summary::ORM()->select('*', "user_idx", $user_idx);
        $loaded_user_data['summary'] = $this->loadDataFetch($summary_data);

        $frequency_data = Frequency::ORM()->select('*', "user_idx", $user_idx);
        $loaded_user_data['frequency'] = $this->loadDataFetch($frequency_data);

        $heroes_data = Heroe::ORM()->select('*', "user_idx", $user_idx);
        $loaded_user_data['heroes'] = $this->loadDataFetch($heroes_data);

        $time_data = Time::ORM()->select('*', "user_idx", $user_idx);
        $loaded_user_data['time'] = $this->loadDataFetch($time_data);

        $layout[] = "header";
        $layout[] = "contents";
        $layout[] = "footer";

        return $this->view->load($layout, $loaded_user_data);
    }

    public function offsetException($arg)
    {
        if (empty($arg[1]))
        {
            header("Location: ".HTTP_HOST); /* Redirect browser */
            exit;
        }
    }

    public function search($arg)
    {
        $this->offsetException($arg);
        $this->constDefineInProcessor($arg);
        $result["search"] = $this->DataToDbFromBlizzard();

        return header('Location: '.HTTP_HOST."main/view/user/".DE_USERNAME);
    }

    public function reSearch($arg)
    {
        $this->offsetException($arg);
        $this->constDefineInProcessor($arg);
        $result_data = Summary::ORM()->select("user_idx", "user_name", DE_USERNAME);
        $result_data = $result_data->fetch();
        $user_idx = (int) $result_data["user_idx"];

        $model_oversearch = new Oversearch();
        $delete_result = $model_oversearch->deleteAllData($user_idx);
        $delete_result ? $result["search"] = $this->DataToDbFromBlizzard() : false;
        return header('Location: '.HTTP_HOST."main/view/user/".DE_USERNAME);
    }

    function constDefineInProcessor($arg)
    {
        (empty($this->user_id) && (!empty($arg[1]))) ? $this->user_id = $arg[1] : false;
        defined('USERNAME') or define('USERNAME', urlencode($this->user_id));
        defined('DE_USERNAME') or define('DE_USERNAME', $this->user_id);
    }

    private function loadDataFetch($fetch_data)
    {
        $fetched_data = array();

        while ($row = $fetch_data->fetch()) {
          $fetched_data[] = $row;
        }
        return $fetched_data;
    }

    /**
    * Collect all the parsed data.
    * @access private
    * @return  Array Boolean $commit_result : commit result array
    */
    private function DataToDbFromBlizzard()
    {
        //get index of insert for new user data
        $select_user_idx_response = Summary::ORM()->getInsertUserIdx();
        $this->user_idx = (((int) $select_user_idx_response["user_idx"]) + 1);

        $parse_data_from_blizzard = $this->GetDataFromBlizzard();

        $explode_data_from_blizzard = explode('column gutter-12@sm gutter-18@md' , $parse_data_from_blizzard);
        //Heroes Summary Processer
        $temp_explode_data = $explode_data_from_blizzard[0].$explode_data_from_blizzard[2];
        $user_summary_data = $this->SummayData2Array($temp_explode_data);

        $pattern = '/<h3 class=\"card-heading\">.*<\/p><\/div><\/div>/';
        preg_match_all($pattern, $explode_data_from_blizzard[2], $matches, PREG_OFFSET_CAPTURE, 3);
        $explode_hero_play_time = explode('svg>' , $matches[0][0][0]);
        $explode_hero_play_time = $explode_hero_play_time[7];

        //Heroes Frequency processer
        $hero_play_time_data = $this->TimeData2Array($explode_hero_play_time);
        $hero_play_time_data_count = $this->hero_time_count;


        //Heroes Statistics processer
        $statistics_data = $this->DetailData2Array($parse_data_from_blizzard);
        $statistics_data_count = $this->hero_statistics_count;

        //Inserts data into the table
        $insert_response["Summary"] = Summary::ORM()->InsertSummary2Table($this->user_idx, $user_summary_data);
        $insert_response["Frequency"] = Frequency::ORM()->InsertFrequency2Table($this->user_idx, $hero_play_time_data, $hero_play_time_data_count);
        $insert_response["Heroes"] = Heroe::ORM()->InsertHeroe2Table($this->user_idx, $statistics_data, $statistics_data_count);
        $insert_response["Time"] = Time::ORM()->InsertTime2Table($this->InsertTimeData());

        $this->RemoveResouce($temp_explode_data);
        $this->RemoveResouce($hero_play_time_data);
        $this->RemoveResouce($hero_play_time_data_count);
        $this->RemoveResouce($statistics_data);

        return $insert_response;
    }

    /**
     * Insert TimeData data parsed from blizzard.
     * @access private
     * @param   Array   $insert_time_data   : TimeData data
     * @return  Boolean $commit_result      : commit result
     */
    private function InsertTimeData()
    {
        $insert_time_data = array(
            ':update_date' => date("Y-m-d H:i:s",time()),
            ':user_idx' => $this->user_idx,
        );
        return $insert_time_data;
    }

    /**
     * data of overwatch to make array data from blizzard
     * @access private
     * @param   String  $data_from_blizzard     : data from blizzard
     * @return  String  $ex_data_from_blizzard  : exploded data from blizzard
     */
    private function DataExplodeFromBlizzard($data_from_blizzard)
    {
        $statistics = explode('row column gutter-18@md' , $data_from_blizzard);
        $statistics = explode('content-box max-width-container hero-comparison-section' , $statistics[2]);  //$statistics[1] normal game
        $statistics = $statistics[0];
        $hero_stat = explode('row gutter-18@md' , $statistics);
        return $hero_stat;
    }

     //needs to refactoring => generator
    private function GetCategoryQueryData($hero_data, $hero) {
        $value = "";
        $hero_data_keys = array_keys($hero_data);
        foreach ($hero_data_keys as $title) {
          $title = strip_tags($title);
          switch ($title) {
            //전투
            case '단독 처치': $category = "전투"; break;
            case '임무 기여 처치': $category = "전투"; break;
            case '준 피해': $category = "전투"; break;
            case '처치': $category = "전투"; break;
            case '환경 요소로 처치': $category = "전투"; break;
            case '동시 처치': $category = "전투"; break;
            case '결정타': $category = "전투"; break;
            case '근접 공격 결정타': $category = "전투"; break;
            case '치명타': $category = "전투"; break;
            case '명중률': $category = "전투"; break;
            case '치명타 명중률': $category = "전투"; break;
            case '목숨당 처치': $category = "전투"; break;
            case '발사': $category = "전투"; break;
            case '명중': $category = "전투"; break;
            case '포탑 파괴': $category = "전투"; break;
            case '분당 치명타': $category = "전투"; break;
            //죽음
            case '죽음': $category = "죽음"; break;
            case '환경 요소로 죽음': $category = "죽음"; break;
            //경기 보상
            case '칭찬 카드': $category = "경기 보상"; break;
            case '메달 획득': $category = "경기 보상"; break;
            case '메달 - 금': $category = "경기 보상"; break;
            case '메달 - 은': $category = "경기 보상"; break;
            case '메달 - 동': $category = "경기 보상"; break;
            //지원
            case '치유': $category = "지원"; break;
            case '처치 시야 지원': $category = "지원"; break;
            case '순간이동기 파괴': $category = "지원"; break;
            case '자가 치유': $category = "지원"; break;
            case '처치 시야 지원 - 평균': $category = "지원"; break;
            case '처치 시야 지원 - 한 게임 최고기록': $category = "지원"; break;
            //최고
            case '처치 - 한 게임 최고기록': $category = "최고"; break;
            case '결정타 - 한 게임 최고기록': $category = "최고"; break;
            case '준 피해 - 한 게임 최고기록': $category = "최고"; break;
            case '치유 - 한 게임 최고기록': $category = "최고"; break;
            case '방어형 도움 - 한 게임 최고기록': $category = "최고"; break;
            case '공격형 도움 - 한 게임 최고기록': $category = "최고"; break;
            case '임무 기여 처치 - 한 게임 최고기록': $category = "최고"; break;
            case '임무 기여 시간 - 한 게임 최고기록': $category = "최고"; break;
            case '동시 처치 - 최고기록': $category = "최고"; break;
            case '단독 처치 - 한 게임 최고기록': $category = "최고"; break;
            case '폭주 시간 - 한 게임 최고기록': $category = "최고"; break;
            case '근접 공격 결정타 - 한 게임 최고기록': $category = "최고"; break;
            case '준 피해 - 한 목숨 최고기록': $category = "최고"; break;
            case '치명타 - 한 게임 최고기록': $category = "최고"; break;
            case '치명타 - 한 목숨 최고기록': $category = "최고"; break;
            case '처치 - 한 목숨 최고기록': $category = "최고"; break;
            case '연속 처치 - 최고기록': $category = "최고"; break;
            case '치유 - 한 목숨 최고기록': $category = "최고"; break;
            case '자가 치유 - 한 게임 최고기록': $category = "최고"; break;
            case '명중률 - 한 게임 최고기록': $category = "최고"; break;
            //게임
            case '승리한 게임': $category = "게임"; break;
            case '폭주 시간': $category = "게임"; break;
            case '임무 기여 시간': $category = "게임"; break;
            case '플레이 시간': $category = "게임"; break;
            case '치른 게임': $category = "게임"; break;
            case '승률': $category = "게임"; break;
            case '무승부 게임': $category = "게임"; break;
            case '패배한 게임': $category = "게임"; break;
            //평균
            case '폭주 시간 - 평균': $category = "평균"; break;
            case '단독 처치 - 평균': $category = "평균"; break;
            case '임무 기여 시간 - 평균': $category = "평균"; break;
            case '임무 기여 처치 - 평균': $category = "평균"; break;
            case '치유 - 평균': $category = "평균"; break;
            case '결정타 - 평균': $category = "평균"; break;
            case '죽음 - 평균': $category = "평균"; break;
            case '준 피해 - 평균': $category = "평균"; break;
            case '처치 - 평균': $category = "평균"; break;
            case '근접 공격 결정타 - 평균': $category = "평균"; break;
            case '자가 치유 - 평균': $category = "평균"; break;
            //기타
            case '방어형 도움': $category = "기타"; break;
            case '방어형 도움 - 평균': $category = "기타"; break;
            case '공격형 도움': $category = "기타"; break;
            case '공격형 도움 - 평균': $category = "기타"; break;
            //스킬
            default: $category = "스킬"; break;
          }
          $this->hero_statistics_data[] = $hero;
          $this->hero_statistics_data[] = $category;
          $this->hero_statistics_data[] = $title;
          $this->hero_statistics_data[] = $hero_data[$title];
        }
        $tmp_result = (!empty($this->hero_statistics_data));
        return $tmp_result;
    }

    /**
     * Get Hero name
     * @access private
     * @param   Array   $stat     : Parsed hero partial data
     * @return  String  $hero     : hero name
     */
    private function GetHeroName($stat) {
        $pattern = '/option-id="[_A-Za-z0-9.+ TorbjörnLúcio:]+">/';
        preg_match_all($pattern, $stat, $hero_name);
        foreach ($hero_name as $row) {
          $row = str_replace("option-id=\"", "", $row);
          $row = str_replace("Torbjörn", "Torbjoern", $row);
          $row = str_replace("Soldier: 76", "Soldier76", $row);
          $row = str_replace("Lúcio", "Lucio", $row);
          $this->hero = str_replace("\">", "", $row);
          }
        return true;
    }

    /**
     * Get hero stat value
     * @access private
     * @param   Array   $stat          : Parsed hero partial data
     * @return  String  $stat_data     : hero stat value
     */
    private function GetStatValue($stat) {
        $pattern = '/<td>[ㄱ-ㅎ|가-힣|a-z|A-Z| -]+<\/td><td>[0-9 +, %.: 시간% ]{1,20}<\/td>/';
        preg_match_all($pattern, $stat, $value, PREG_OFFSET_CAPTURE, 3);
        // $keywords = preg_split("/<\/td><td>/", ($value[0][0][0]));
        foreach ($value[0] as $stat_value) {
          $split = explode ("</td><td>", $stat_value[0]);
          $title = strip_tags($split[0]);
          $value = strip_tags($split[1]);
          $stat_data[$title] = $value;
        }
        return $stat_data;
    }

    /**
     * data of overwatch to make array data from blizzard
     * @access protected
     * @param   String  $data_from_blizzard     : data from blizzard
     * @return  Array   $hero_statistics_data   : User Heroes Data
     */
    protected function DetailData2Array($data_from_blizzard) {
        $hero_data = array();
        $value = "";
        $hero_stat = $this->DataExplodeFromBlizzard($data_from_blizzard);
        if (empty($this->hero))
            $this->GetHeroName($hero_stat[0]); array_shift($hero_stat);
        foreach ($hero_stat as $row)
        {
          $hero = $this->hero[$this->index];
          $hero_data[$hero][] = $this->GetStatValue($row);
          $this->index++;
        }
        array_pop($hero_data);
        $heroes = array_keys($hero_data);
        $heroes_count = count($heroes);
        for ($i=0; $i < $heroes_count ; $i++) {
          $this->GetCategoryQueryData($hero_data[$heroes[$i]][0], $heroes[$i]);
        }
        $this->RemoveResouce($data_from_blizzard);
        $this->RemoveResouce($hero_stat);
        $this->RemoveResouce($hero_data);
        $this->hero_statistics_count = (count($this->hero_statistics_data)/4);
        return $this->hero_statistics_data;
    }


    /**
     * Insert Frequency data parsed from blizzard.
     * @access private
     * @param   Array   $hero_play_time_data        : Frequency data
     * @param   Integer $hero_play_time_data_count  : Frequency data count
     * @return  Boolean $commit_result              : commit result
     */
    protected function SummayData2Array($data_from_blizzard) {
        $pattern = '/<h3 class=\"card-heading\">.*<\/p><\/div><\/div>/';
        preg_match_all($pattern, $data_from_blizzard, $matches, PREG_OFFSET_CAPTURE, 3);
        $section_data = explode('svg>' , $matches[0][0][0]);
        $summary_data = $this->getUserSummary($section_data, $data_from_blizzard);
        $this->RemoveResouce($data_from_blizzard);
        $this->RemoveResouce($section_data);
        return $summary_data;
    }

    /**
     * data of overwatch to make array data from blizzard
     * @access protected
     * @param   String  $data_from_blizzard     : data from blizzard
     * @return  Array   $fanal_play_time_data   : array for insert
     */
    protected function TimeData2Array($data_from_blizzard)
    {
        $temp_play_time_data = $this->ReturnBaseArray($data_from_blizzard);
        $play_time_data = $this->TimeDataChangeShape($temp_play_time_data);
        $fanal_play_time_data = $this->ReturnMatchingArray($play_time_data);
        $this->hero_time_count = count($play_time_data);
        $this->RemoveResouce($data_from_blizzard);
        $this->RemoveResouce($temp_play_time_data);
        $this->RemoveResouce($play_time_data);
        return $fanal_play_time_data;
    }

    /**
     * Returns in a one-dimensional array.
     * @access private
     * @param   String  $data_from_blizzard     : parsed data from blizzard
     * @return  Array   $temp_play_time_data    : one-dimensional array
     */
    private function ReturnBaseArray($data_from_blizzard)
    {
        $heroes = explode('data-show-more' , $data_from_blizzard);
        $heroes = $heroes[0];
        $temp_play_time_data = array();
        $heroes = explode('progress-2 m-animated progress-category-item' , $heroes);
        array_shift($heroes);
        foreach ($heroes as $hero)
        {
            if($this->GetPlayTimeOfUser($hero) != false)
                $temp_play_time_data[] = $this->GetPlayTimeOfUser($hero);
        }
        return $temp_play_time_data;
    }

    /**
     * parse user's play time data.
     * @access private
     * @param   String $hero_data           : hero data from blizzard
     * @param   String $hero_name           : hero name
     * @return  String $hero_name_value     : return each hero data
     */
    private function GetPlayTimeOfUser($hero_data, $hero_name = 1)
    {
        preg_match("/<div class=\"title\">.*<\/div><div class=\"description\">/", $hero_data, $hero, PREG_OFFSET_CAPTURE, 3);
        preg_match("/<div class=\"description\">.*<\/div>/", $hero_data, $time, PREG_OFFSET_CAPTURE, 3);
        $hero_name = strip_tags($hero[0][0]);
        $hero_value = strip_tags($time[0][0]);
        $hero_value = str_replace("+ 모든 영웅 보기", "", $hero_value);
        if ($hero_name != 'overwatch.guid.undefined')
            return $hero_name.'|'.$hero_value;
        else
            return false;
    }

    /**
      * Returns the data in a one-dimensional array.
      * @access protected
      * @param   String  $play_time_data         : play time data from blizzard
      * @param   Integer $freq_index             : freq index
      * @return  Array   $fanal_play_time_data   : matching array
      */
     private function ReturnMatchingArray($play_time_data, $freq_index = 1)
     {
         $play_time = array();
         foreach ($play_time_data as $key => $hero)
         {
             $fanal_play_time_data[] = $key;
             $fanal_play_time_data[] = $freq_index;
             foreach ($play_time_data[$key] as $key => $value)
             {
                 $fanal_play_time_data[] = $value;
             }
             $freq_index++;
         }
         return $fanal_play_time_data;
     }


    /**
     * change array shape.
     * @access private
     * @param   Array $temp_play_time_data  : play time data
     * @return  Array $play_time_data       : changed array data
     */
    private function TimeDataChangeShape($temp_play_time_data)
    {
        $exception_hero = array();
        foreach ($temp_play_time_data as $hero => $value)
        {
            $temp_data = explode("|", $value);
            if(in_array($temp_data[0], $exception_hero)) {
                continue;
            } else {
                if ($temp_data[1] == '--')
                $exception_hero[] = $temp_data[0];
                else
                $play_time_data[$temp_data[0]][] = $temp_data[1]; // $var['hero'][] = 'value';
            }
        }
        return $play_time_data;
    }


    /**
     * get summary from parssing data
     * @access private
     * @param   String  $section_data           : Partial data from blizzard
     * @param   String  $data_from_blizzard     : data from blizzard
     * @return  String  $summary                : summary user_summary_data data
     */
    private function getUserSummary($section_data, $data_from_blizzard) {
      $level_img = "";
      $icon = "";
      $level = "";
      $com_grade = "";
      preg_match('/https:\/\/blzgdapipro-a.akamaihd.net\/game\/playerlevelrewards\/0x0250000000000[A-Za-z0-9]+_Rank.png/',
                  $data_from_blizzard, $level_img);
      preg_match('/src=\"https:\/\/blzgdapipro-a.akamaihd.net\/game\/unlocks\/.*" class\=\"player-portrait\"/', $data_from_blizzard, $icon);
      preg_match('/<div class=\"u-vertical-center\">[0-9]{1,10}<\/div>/', $data_from_blizzard, $level);
      preg_match('/<div class=\"u-align-center h6\">[0-9]{1,10}<\/div>/', $data_from_blizzard, $com_grade);
      $icon = str_replace('src="', "", $icon);
      $icon = str_replace('" class="player-portrait"', "", $icon);
      $avg_solo_kill = explode('상위' , $section_data[7]);
      $summary =
          (array(
                ':icon' => $icon[0],
                ':level' => strip_tags($level[0]),
                ':com_grade' => strip_tags($com_grade[0]),
                ':user_name' => DE_USERNAME,
                ':avg_kill' => str_replace("처치 - 평균", "", strip_tags($section_data[0])),
                ':avg_damage' => str_replace("준 피해 - 평균", "", strip_tags($section_data[1])),
                ':avg_death' => str_replace("죽음 - 평균", "", strip_tags($section_data[2])),
                ':avg_Murderous' => str_replace("결정타 - 평균", "", strip_tags($section_data[3])),
                ':avg_heal' => str_replace("치유 - 평균", "", strip_tags($section_data[4])),
                ':avg_contributions_kill' => str_replace("임무 기여 처치 - 평균", "", strip_tags($section_data[5])),
                ':avg_contributions_time' => str_replace("임무 기여 시간 - 평균", "", strip_tags($section_data[6])),
                ':avg_solo_kill' => str_replace("단독 처치 - 평균", "", strip_tags($avg_solo_kill[0])),
                ':level_img' => $level_img[0],
                ':analy' => $this->GetUserAnaly(str_replace("준 피해 - 평균", "", strip_tags($section_data[1])),
                            str_replace("임무 기여 처치 - 평균", "", strip_tags($section_data[5])),
                            str_replace("임무 기여 시간 - 평균", "", strip_tags($section_data[6]))),
          ));
      return $summary;
    }

    /**
     * Analyze based on user summary data
     * @access private
     * @param   Integer  $avg_damage                : average damage
     * @param   Integer  $avg_contributions_kill    : average contributions_kill
     * @param   Integer  $avg_contributions_time    : average contributions_time
     * @return  String   $analy                     : Analyze data
     */
    private function GetUserAnaly($avg_damage = 1, $avg_contributions_kill = 1, $avg_contributions_time = 1) {
        $avg_damage = str_replace(",", "", $avg_damage);
        $avg_damage = (int) $avg_damage;
        $temp = explode(":", $avg_contributions_time);
        $minuts = (int) $temp[0];
        $minuts = ($minuts * 60);
        $sec = (int) $temp[1];
        $avg_contributions_time = ($minuts + $sec);
        //damage)
        switch ($avg_damage) {
            case  $avg_damage<= 3000:
                $analy[0] = 1;
                break;
            case  $avg_damage > 3000 && $avg_damage <= 6000:
                $analy[0] = 2;
                break;
            case  $avg_damage > 6000 && $avg_damage <= 9000:
                $analy[0] = 3;
                break;
            case  $avg_damage > 9000 && $avg_damage <= 12000:
                $analy[0] = 4;
                break;
            case  $avg_damage > 12000:
                $analy[0] = 5;
                break;
        }
        //avg_contributions_kill
        switch ($avg_contributions_kill) {
            case  $avg_contributions_kill <= 5:
                $analy[1] = 1;
                break;
            case  $avg_contributions_kill > 5 && $avg_contributions_kill <= 10:
                $analy[1] = 2;
                break;
            case  $avg_contributions_kill > 10 && $avg_contributions_kill <= 15:
                $analy[1] = 3;
                break;
            case  $avg_contributions_kill > 15 && $avg_contributions_kill <= 20:
                $analy[1] = 4;
                break;
            case  $avg_contributions_kill > 20:
                $analy[1] = 5;
                break;
        }
        //avg_contributions_time
        switch ($avg_contributions_time) {
            case  $avg_contributions_time <= 10:
                $analy[2] = 1;
                break;
            case  $avg_contributions_time > 10 && $avg_contributions_time <= 30:
                $analy[2] = 2;
                break;
            case  $avg_contributions_time > 30 && $avg_contributions_time <= 60:
                $analy[2] = 3;
                break;
            case  $avg_contributions_time > 60 && $avg_contributions_time <= 120:
                $analy[2] = 4;
                break;
            case  $avg_contributions_time > 120:
                $analy[2] = 5;
                break;
        }
        $analy = floor(($analy[0]+$analy[1]+$analy[2])/3);
        $grade = [
            "nodata",
            "bug",
            "slave",
            "human",
            "nobility",
            "god",
        ];
        return $grade[($analy)];
    }

    /**
    * Get Data From Blizzard.
    * @access private
    * @param   String  $url                    : blizzard url
    * @return  String  $blizzard_parse_data    : data from blizzard
    */
    private function GetDataFromBlizzard()
    {
        $url = "https://playoverwatch.com/ko-kr/career/pc/kr/".USERNAME;
        try
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $blizzard_parse_data = curl_exec($ch);
            curl_close($ch);
            return $blizzard_parse_data;
        }
        catch (Exception $e)
        {
            "<hr /><center><div style='width:500px;border: 3px solid #FFEEFF; padding: 3px; background-color: #FFDDFF;
            font-family: verdana; font-size: 10px'><b>cURL Error</b><br>".$e."</div></center>";
        die;
        }
    }

    /**
     * Resouce Remover for package
     * @access public
     */
    public function RemoveResouce($obj)
    {
        unset($obj);
    }
}
