<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Support\Facades\DB;

class DatabaseService extends BaseService
{
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @var static $instance
     */
    private static $instance;

    /**
     * @return DatabaseService
     */
    public static function getInstance(): DatabaseService
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * TODO:数据列表
     * @return array
     */
    public function getDatabaseLists(): array
    {
        $result = DB::select("SHOW TABLE STATUS");
        foreach ($result as $item) {
            $this->return['lists'][] = array(
                'name'           => $item->Name,
                'engine'         => $item->Engine,
                'version'        => $item->Version,
                'data_length'    => formatBates($item->Data_length),
                'auto_increment' => $item->Auto_increment,
                'create_time'    => $item->Create_time,
                'comment'        => $item->Comment,
                'collation'      => $item->Collation
            );
        }
        return $this->return;
    }

    /**
     * todo:数据表备份
     * @param $form
     * @return array|int
     */
    public function backUpTable($form)
    {
        function_exists('set_time_limit') && set_time_limit(0);
        $s_time = time();
        $tablePath = base_path('database/backup');
        if (!is_dir($tablePath)) {
            mkdir($tablePath);
        }
        $savaFilePath = $tablePath . DIRECTORY_SEPARATOR . date('Y_m_d') . '_create_table_' . $form['name'] . '.sql';
        $content = '';
        if ($form['form'] === 'table') {
            $content = $this->createTable($form['name']);
        }
        if ($form['form'] === 'source') {
            $content = $this->sourceTable($form['name']);
        }
        if ($form['form'] === 'all') {
            $content = $this->createTable($form['name']) . $this->sourceTable($form['name']);
        }
        $result = writeFile($savaFilePath, $content);
        $form['time'] = time() - $s_time;
        return !empty($result['code']) ? $result : ['code' => Code::SUCCESS, 'message' => 'backup table successfully', 'lists' => $form];
    }

    /**
     * TODO:创建数据表SQL
     * @param string $tableName
     * @return string
     */
    protected function createTable(string $tableName): string
    {
        $result = DB::select(sprintf('SHOW CREATE TABLE %s', $tableName));
        $sql = "-- ----------------------------\n-- " . date('Y-m-d H:i:s') . " backup table start\n-- ----------------------------\n";
        foreach ($result as $item) {
            $item = (array)$item;
            $sql .= "-- ----------------------------\n-- Table structure for {$item['Table']}\n-- ----------------------------\n";
            $sql .= sprintf("DROP TABLE IF EXISTS `%s`", $item["Table"]) . ';';
            $sql .= "\n" . $item['Create Table'];
            $sql .= ";\n -- ";
        }
        return $sql;
    }

    /**
     * TODO:数据表数据SQL
     * @param string $tableName
     * @return string
     */
    protected function sourceTable(string $tableName): string
    {
        $result = DB::select(sprintf('SELECT * FROM %s', $tableName));
        $sql = ";\n-- ----------------------------\n-- Records of $tableName\n-- ----------------------------\n";
        if (empty($result)) {
            $sql .= "-- ----------------------------\n-- " . date('Y-m-d H:i:s') . " backup table end\n-- ----------------------------\n";
            return $sql;
        }
        foreach ($result as $item) {
            $sql .= sprintf('INSERT INTO %s VALUES %s', $tableName, '(');
            foreach ($item as $rows) {
                $sql .= "'{$rows}',";
            }
            $sql .= ');';
            //删除最后三个字符串
            $sql = substr($sql, 0, strlen($sql) - 3);
            $sql .= ");\n";
        }
        $sql .= "-- ----------------------------\n-- " . date('Y-m-d H:i:s') . " backup table end\n-- ----------------------------\n";
        return rtrim($sql, ',');
    }

    /**
     * TODO:数据表修复
     * @param $form
     * @return array
     */
    public function repairTable($form): array
    {
        $result = DB::select(sprintf('REPAIR TABLE %s', $form['name']));
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'repair table failed';
            return $this->return;
        }
        $this->return['lists'] = $form;
        $this->return['message'] = 'repair table successfully';
        return $this->return;
    }

    /**
     * TODO:数据表优化
     * @param $form
     * @return array
     */
    public function optimizeTable($form): array
    {
        $result = $form['engine'] == 'MyISAM' ? DB::select(sprintf('OPTIMIZE TABLE %s', $form['name'])) : DB::select(sprintf('ANALYZE TABLE %s', $form['name']));
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'optimize table failed';
            return $this->return;
        }
        $this->return['lists'] = $form;
        $this->return['message'] = 'optimize table successfully';
        return $this->return;
    }

    /**
     * TODO:修改数据表注释
     * @param $form
     * @return array
     */
    public function commentTable($form): array
    {
        DB::select("ALTER TABLE {$form['name']} COMMENT '{$form['comment']}'");
        $this->return['lists'] = $form;
        $this->return['message'] = 'update table successfully';
        return $this->return;
    }
}
