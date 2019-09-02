<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Database
 * @author <fl140125@gmail.com>
 * @package App\Models
 */
class Database extends Model
{
    /**
     * @var $instance
     */
    private static $instance;
    /**
     * @var string $start_line
     */
    private $start_line;
    /**
     * @var string $end_line
     */
    private $end_line;
    /**
     * @var string $str_line
     */
    private $str_line;
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @return Database
     */
    static public function getInstance()
    {
        if (!self::$instance instanceof self){
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * Database constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->start_line = "-- ----------------------------\n-- ".date('Y-m-d H:i:s')." backup table start\n-- ----------------------------\n";
        $this->end_line = "-- ----------------------------\n-- ".date('Y-m-d H:i:s')." backup table end\n-- ----------------------------\n";
        $this->str_line = "-- ";
    }

    /**
     * TODO:数据列表
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
     * TODO:创建数据表SQL
     * @param $tableName
     * @return string
     */
    public  function createTable($tableName)
    {
        $result = DB::select(sprintf("SHOW CREATE TABLE %s", $tableName));
        $sql = $this->start_line;
        foreach ($result as $item){
            $item = object_to_array($item);
            $sql.="-- ----------------------------\n-- Table structure for {$item['Table']}\n-- ----------------------------\n";
            $sql.= sprintf("DROP TABLE IF EXISTS `%s`",$item["Table"]).";";
            $sql.= "\n".$item['Create Table'];
            $sql.=";\n".$this->str_line;
        }
        return $sql;
    }

    /**
     * TODO:数据表数据SQL
     * @param $tableName
     * @return bool|string
     */
    public function sourceTable($tableName)
    {
        $result = DB::select(sprintf("SELECT * FROM %s", $tableName));
        $sql=";\n-- ----------------------------\n-- Records of $tableName\n-- ----------------------------\n";
        if (empty($result)){
            $sql.= $this->end_line;
            return $sql;
        }
        foreach ($result as $item){
            $sql.= sprintf("INSERT INTO %s VALUES %s",$tableName,"(");
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

    /**
     * TODO:数据表修复
     * @param $tableName
     * @return array
     */
    public function repairTable($tableName)
    {
        $result = DB::select(sprintf("REPAIR TABLE %s", $tableName));
        return $result;
    }
    /**
     * TODO:数据表优化
     * @param $tableName
     * @return array
     */
    public function optimizeTable($tableName)
    {
        $result = DB::select(sprintf("OPTIMIZE TABLE %s", $tableName));
        return $result;
    }
    /**
     * TODO:修改数据表注释
     * @param $tableName
     * @param $comment
     * @return array
     */
    public function commentTable($tableName,$comment)
    {
        $result = DB::select(sprintf("ALTER TABLE %s COMMENT '%s'", $tableName,$comment));
        return $result;
    }
}
