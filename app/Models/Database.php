<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Database extends Model
{
    private static $instance;
    private $start_line;
    private $end_line;
    private $str_line;
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }
    static public function getInstance()
    {
        if (!self::$instance instanceof self){
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->start_line = "-- ----------------------------\n-- ".date('Y-m-d H:i:s')." backup table start\n-- ----------------------------\n";
        $this->end_line = "-- ----------------------------\n-- ".date('Y-m-d H:i:s')." backup table end\n-- ----------------------------\n";
        $this->str_line = "-- ";
    }

    /**
     * 数据列表
     * @return array
     */
    public function lists()
    {
        $result = DB::select('SHOW TABLE STATUS');
        $tables=array();
        foreach ($result as $key => $item){
            $tables[$key]['name'] = $item->Name;
            $tables[$key]['engine'] = $item->Engine;
            $tables[$key]['version'] = $item->Version;
            $tables[$key]['data_length'] = format_bytes($item->Data_length);
            $tables[$key]['auto_increment'] = $item->Auto_increment;
            $tables[$key]['create_time'] = $item->Create_time;
            $tables[$key]['comment'] = $item->Comment;
            $tables[$key]['collation'] = $item->Collation;
        }
        return $tables;
    }
    /**
     * 创建数据表SQL
     * @param $tableName
     * @return string
     */
    public  function createTable($tableName)
    {
        $result = DB::select("SHOW CREATE TABLE {$tableName}");
        $sql = $this->start_line;
        foreach ($result as $item){
            $item = object_to_array($item);
            $sql.="-- ----------------------------\n-- Table structure for {$item['Table']}\n-- ----------------------------\n";
            $sql.= "DROP TABLE IF EXISTS `".$item["Table"]."`;";
            $sql.= "\n".$item['Create Table'];
            $sql.=";\n".$this->str_line;
        }
        return $sql;
    }

    /**
     * 数据表数据SQL
     * @param $tableName
     * @return bool|string
     */
    public function sourceTable($tableName)
    {
        $result = DB::select("SELECT * FROM `".$tableName."`");
        $sql=";\n-- ----------------------------\n-- Records of $tableName\n-- ----------------------------\n";
        if (empty($result)){
            return $sql;
        }
        foreach ($result as $item){
            $sql.="INSERT INTO `".$tableName."` VALUES (";
            foreach ($item as $keys => $rows) {
                $sql.= "'$rows',";
            }
            $sql.=");";
            $sql= substr($sql,0,strlen($sql)-3); //删除最后三个字符串
            $sql.=");\n";
        }
        $sql.= $this->end_line;
        $sql=rtrim($sql, ',');
        return $sql;
    }
}
