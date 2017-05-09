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
namespace Javamon\Jframe\Core;

use \Javamon\Jframe\Core\Loader as Loader;
use \Javamon\Jframe\Core\Config as Config;

/**
 *  라우터 클래스 : 요청된 주소에 따라 로더로 데이터 전달
 */
class Route
{
    private static $config;

    static public function getRequest()
    {
        empty(static::$config) ? static::$config = (new Config())->configure() : false;

        $segment = filter_input(INPUT_GET, "url");

        $route = array();
        $route = explode("/",$segment);

        $structure = explode("/",static::$config["default"]["controller"]);

        $request_count = (int) count($route);
        $request_length = ($request_count-1);

        ($route[$request_length] === "") ? $request_count = ($request_count -1) : false;

        // 요청이 1개 일때 클래스와 함수가 생략되었을때는 디폴트(데이터만 있을때)
        if ($request_count === 0)
        {
            $class_name = "\\Javamon\\Jframe\\Processor\\".ucfirst($structure[0]);
            $method = ucfirst($structure[1]);
        }
        elseif($request_count === 1 && !class_exists("\\Javamon\\Jframe\\Processor\\".ucfirst($route[0])))
        {
            $class_name = "\\Javamon\\Jframe\\Processor\\".ucfirst($structure[0]);
            $method = ucfirst($structure[1]);
            array_shift($route);array_shift($route);
        }
        elseif($request_count === 1 && class_exists("\\Javamon\\Jframe\\Processor\\".ucfirst($route[0])))
        {
            $class_name = "\\Javamon\\Jframe\\Processor\\".ucfirst($route[0]);
            empty($structure[1]) ? $method = ucfirst($route[0]) : $method = ucfirst($structure[1]);
            array_shift($route);array_shift($route);
        }
        elseif ($request_count === 2 && !class_exists("\\Javamon\\Jframe\\Processor\\".ucfirst($route[0])))
        {
            $class_name = "\\Javamon\\Jframe\\Processor\\".ucfirst($structure[0]);
            $method = ucfirst($structure[1]);
        }
        // 요청이 2개 일때 클래스와 함수가 있을때
        elseif($request_count === 2 && class_exists("\\Javamon\\Jframe\\Processor\\".ucfirst($route[0])))
        {
            $class_name = "\\Javamon\\Jframe\\Processor\\".ucfirst($route[0]);
            $method = ucfirst($route[1]);
            array_shift($route);array_shift($route);
        }
        // 요청이 3개 이상, 클래스와 함수 그리고 데이터까지 있을때 = 사용자 정의부
        elseif ($request_count === 3 && !class_exists("\\Javamon\\Jframe\\Processor\\".ucfirst($route[0])))
        {
            $class_name = "\\Javamon\\Jframe\\Processor\\".ucfirst($structure[0]);
            $method = ucfirst($route[0]);
            array_shift($route);
        }
        elseif ($request_count === 3 && class_exists("\\Javamon\\Jframe\\Processor\\".ucfirst($route[0])))
        {
            $class_name = "\\Javamon\\Jframe\\Processor\\".ucfirst($route[0]);
            $method = ucfirst($route[1]);
            array_shift($route);
            array_shift($route);
        }
        elseif ($request_count > 3)
        {
            $class_name = "\\Javamon\\Jframe\\Processor\\".ucfirst($route[0]);
            $method = ucfirst($route[1]);
            array_shift($route);array_shift($route);
        }
        else {
            return trigger_error("The defined function can not be found : ".ucfirst($route[0])."()", E_USER_ERROR);
        }

        $load = new loader();
        $load->init($class_name, $method, $route);
    }
}