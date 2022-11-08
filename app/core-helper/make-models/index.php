<?php
// name: Make models

/**
 * Make models automatically by database tables
 * use rules for auto relations
 * - id - primary key. always
 * - table names and column names with single quuantity
 * - related column names must contain table name related table and column. For example user.id <=> address.user_id
 * - use column comments
 * - write own code between `BEGIN CUSTOM CODE` and `END CUSTOM CODE` Else it will be overwritten
 */


function many_form($word)
{
    $x = substr($word, -1);
    if ($x == 's' or $x == 'z' or $x == 'x') return $word . 'es';
    $xx = substr($word, -2);
    if ($xx == 'ch' or $xx == 'sh') return $word . 'es';
    return $word . 's';
}

$types_mapping = array(
    'float' => 'float',
    'double' => 'float',
    'int' => 'int',
    'bigint' => 'int',
    'smallint' => 'int',
    'timestamp' => 'int',
    'tinyint' => 'int',
    'varchar' => 'string',
    'char' => 'string',
    'text' => 'string',
);

$tablesData = array();

$sql = "show tables";
foreach (c\db::ec($sql) as $table) {
    if (is_callable(c\core::$data['db_describe_cache'])) {
        $cache_file = c\core::$data['db_describe_cache']($table, c\db::autodb(c\core::$data['db']), c\core::$data['db']);
        if (file_exists($cache_file)) unlink($cache_file);
    }
    $tablesData[$table] = c\db::describeTable($table);
}
foreach ($tablesData as $table => $tableData) {
    foreach ($tableData['data'] as $column_key => $column_val) {
        if (substr($column_key, -3) == '_id') {
            $relation = substr($column_key, 0, -3);
			if ($tablesData[$relation]['data']['id']) {
                // relation found
                $tablesData[$table]['relations_to_one'][$relation] = $relation;
                $tablesData[$relation]['relations_to_many'][$table] = 'id';
            }
            // partition match search
			for ($i=1;$i<strlen($relation);$i++){
				if (substr($relation,$i-1,1)!='_')continue;
				$subrelation=substr($relation,$i);
				foreach ($tablesData as $table2 => $tableData2) {
					if ($subrelation == $table2) {
						if (isset($tablesData[$table]['relations_to_one'][$relation]))continue;
						$tablesData[$table]['relations_to_one'][$relation] = $table2;
					}
				}
			}
        } elseif (in_array($column_key, ['position', 'weight', 'order'])) {
            $tablesData[$table]['order'][] = $column_key;
        }
    }
}
print_r($tablesData);

function getRelationName($relation,$table=null){
    if ($relation == 'order' or $relation == 'cursor' or $relation==$table) return $relation . '_relation';
    return $relation;
}

foreach ($tablesData as $table => $tableData) {
    $modelFilename = c\core::$data['include_dir'] . '/' . $table . '.php';
    $begin = true;
    $end = true;
    $custom = '
	
';
    $source_content = '';
    if (file_exists($modelFilename)) {
        $source_content = file_get_contents($modelFilename);
        $begin = strpos($source_content, '/* BEGIN CUSTOM CODE */');
        $end = strpos($source_content, '/* END CUSTOM CODE */');

        $custom = substr(
            $source_content,
            $begin + strlen('/* BEGIN CUSTOM CODE */' . PHP_EOL),
            $end - $begin - 2 - strlen('/* END CUSTOM CODE */' . PHP_EOL)
        );
    }
    if ($begin && $end) {
        // generate model
        $content = '<?php
class ' . $table . ' extends c\model{
';
        foreach ($tableData['data'] as $field_key => $field_val) {
            $content .= '	const FIELD_' . strtoupper($field_key) . '=\'' . $field_key . '\';
';
        }
        if ($tableData['primary_key'][0]) $content .= '	var $primaryField=\'' . $tableData['primary_key'][0] . '\';';
		$content.='

/* BEGIN CUSTOM CODE */' . PHP_EOL . $custom . '/* END CUSTOM CODE */' . PHP_EOL;

        foreach ($tableData['data'] as $column_key => $column_val) {
            if ($column_val['type'] == 'date' || $column_val['type'] == 'datetime') {
                $content .= '	/**
	 * 
	 * @return \DateTime
	 */
	function ' . $column_key . '(){
		return c\date::to_datetime($this->' . $column_key . ');
	}
';
            }
            if (substr($column_key, -5) == '_json') {
                $content .= '	/**
	 * 
	 * @return array
	 */
	function ' . substr($column_key, 0, -5) . '(){
		return $this->' . $column_key . '==\'\'?null:c\input::jsonDecode($this->' . $column_key . ');
	}
';
            }
        }

        foreach ($tableData['relations_to_one'] as $relation_name => $relation_table) {
            $content .= '	/**
	 * 
	 * @return ' . $relation_table . '
	 */
	function ' . getRelationName($relation_name,$table) . '(){
		return $this->relationToOne(\'' . $relation_table . '\', \'id\', \'' . $relation_name . '_id\');
	}
';
        }
        foreach ($tableData['relations_to_many'] as $relation_table => $relation_field) {
            $content .= '	/**
	 * 
	 * @return ' . $relation_table . '|' . $relation_table . '[]
	 */
	function ' . many_form($relation_table) . '(){
		return $this->relation(\'' . $relation_table . '\', \'' . $table . '_id\', \'id\');
	}
';
        }
        foreach ($tableData['order'] as $order_field) {
            $content .= '	/**
	 * 
	 * @return ' . $relation_table . '
	 */
	function order_by_' . $order_field . '($order=\'\',$prior=999){
		return $this->order(\'' . $order_field . '\', $order, $prior);
	}
';
        }
        foreach ($tableData['unique_key'] as $unique_key) {
            if (sizeof($unique_key) != 1) continue;
            $content .= '	/**
	 * 
	 * @return ' . $table . '
	 */
	static function find_by_' . $unique_key[0] . '_static($value){
		return self::whereStatic(\'' . $unique_key[0] . '\', "=", $value)->first();
	}
';
        }
        foreach ($tableData['data'] as $field_key => $field_val) {
            if (substr($field_key, 0, 3) == 'is_') {
                $content .= '	/**
	 * Filter is ' . substr($field_key, 3) . '
	 * @return ' . $table . '|' . $table . '[]
	 */
	function ' . $field_key . '(){
        	if ($this->isRowMode())return (bool)$this->' . $field_key . ';
		return $this->where(\'' . $field_key . '\',1);
	}
	/**
	 * Filter not is ' . substr($field_key, 3) . '
	 * @return ' . $table . '|' . $table . '[]
	 */
	function not_' . $field_key . '(){
		if ($this->isRowMode())return !(bool)$this->' . $field_key . ';
		return $this->where(\'' . $field_key . '\',0);
	}
';
            }
        }
        $content .= '
}';
        if ($content != $source_content) {
            file_put_contents($modelFilename, $content);
			chmod($modelFilename,0777);
            echo "Model " . $table . ' created';
        }
    }
    $modelFilename = c\core::$data['include_dir'] . '/hint/' . $table . '.php';
    $begin = true;
    $end = true;
    $source_content = '';
    $custom = '
	
';
    if (file_exists($modelFilename)) {
        $source_content = file_get_contents($modelFilename);
        $begin = strpos($source_content, '/* BEGIN CUSTOM CODE */');
        $end = strpos($source_content, '/* END CUSTOM CODE */');

        $custom = substr(
            $source_content,
            $begin + strlen('/* BEGIN CUSTOM CODE */' . PHP_EOL),
            $end - $begin - 2 - strlen('/* END CUSTOM CODE */' . PHP_EOL)
        );
    }
    if ($begin && $end) {
        // generate hint model
        $content = "<?php
class " . $table . '{

/* BEGIN CUSTOM CODE */' . PHP_EOL . $custom . '/* END CUSTOM CODE */' . PHP_EOL;
        foreach ($tableData['data'] as $field_key => $field_val) {
            if (isset($field_val['comment']) || isset($types_mapping[$field_val['type']])) {
                $content .= '	/**' . (isset($field_val['comment']) ? '
  * ' . trim($field_val['comment']) : '') . (isset($types_mapping[$field_val['type']]) ? '
  * @var ' . $types_mapping[$field_val['type']] : '') . '
  */';
            }
            $content .= '
  var $' . $field_key . ';

';
        }
        $content .= ' /**
     * @return ' . $table . '
     */
   static function findStatic(){}
   /**
     * @return ' . $table . '
     */
   static function firstStatic(){}
   /**
     * @return ' . $table . '
     */
   static function toObject(){}
   /**
     * @return ' . $table . '
     */
    static function findOrCreateStatic(){}
   /**
     * @return ' . $table . '
     */
    static function findOrFailStatic(){}
   /**
     * @return ' . $table . '_collection|' . $table . '[]
     */
    static function whereStatic(){}
   /**
     * @return ' . $table . '_collection|' . $table . '[]
     */
    static function whereNullStatic(){}
   /**
     * @return ' . $table . '_collection|' . $table . '[]
     */
    static function whereNotNullStatic(){}
   /**
     * @return ' . $table . '_collection|' . $table . '[]
     */
    function whereHas(){}
   /**
     * @return ' . $table . '_collection|' . $table . '[]
     */
    static function whereHasStatic(){}
   /**
     * @return ' . $table . '_collection|' . $table . '[]
     */
    function whereNull(){}
   /**
     * @return ' . $table . '_collection|' . $table . '[]
     */
    function whereNotNull(){}
   /**
     * @return ' . $table . '_collection|' . $table . '[]
     */
    static function orderStatic(){}
   /**
     * @return ' . $table . '_collection|' . $table . '[]
     */
    static function getStatic(){}
   /**
     * @return ' . $table . '_collection|' . $table . '[]
     */
    function get(){}
   /**
     * @return ' . $table . '
     */
    function first(){}
    function delete(){}
    /**
     * @return ' . $table . '
     */
    function firstOrCreate(){}
	/**
     * @return ' . $table . '
     */
    function firstOrFail(){}
	/**
     * @return ' . $table . '_collection|' . $table . '[]
     */
    function order(){}
	/**
     * @return ' . $table . '_collection|' . $table . '[]
     */
    function where(){}
	/**
     * @return ' . $table . '_collection|' . $table . '[]
     */
    function whereNotIn(){}
     /**
       * @return ' . $table . '_collection|' . $table . '[]
       */
      function whereIn(){}
     /**
       * @return ' . $table . '_collection|' . $table . '[]
       */
      static function whereInStatic(){}
     /**
       * @return ' . $table . '_collection|' . $table . '[]
       */
    static function whereNotInStatic(){}
}
class ' . $table . '_collection extends c\collection_object{
    /**
     * @return ' . $table . '
     */
    function first(){}
    /**
     * @return ' . $table . '
     */
    function offsetGet($offset){}
    /**
     * @return ' . $table . '
     */
    function current(){}
}';

        if ($content != $source_content) {
            file_put_contents($modelFilename, $content);
            echo "Model hint " . $table . ' created';
        }
    }
}
