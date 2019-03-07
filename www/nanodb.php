<?php
/*
Note: this will not lock the file, so might cause problems if several scripts are accessing the datafile simultaneously.
*/
Class nanoDb
{
  private $dataFile = NULL;
  private $limit = NULL;

  // ------------------------------------------------------------------------
	// Constructor
	
	public function __construct($setDataFile, $setLimit)
	{
    $this->dataFile = $setDataFile;
    $this->limit = $setLimit;

    if (file_exists($this->dataFile) === FALSE) {
      $emptyArr = Array();
      file_put_contents($this->dataFile, json_encode($emptyArr));
    }
  }

  public function getAll() {
    $json = file_get_contents($this->dataFile);
    return json_decode($json, TRUE);
  }

  
  public function getById($id) {
    // Validate id
    if (is_string($id) === FALSE) {
      return FALSE;
    }

    // Get existing data
    $arr = $this->getAll();

    // Return record
    if (isset($arr[$id])) {
      return $arr[$id];
    }
    else {
      return FALSE;
    }

  }

  // On success returns number of bytes in db file,
  // on failure returns FALSE
  public function addRecord($id, $record) {
    // Validate record
/*    if (!is_array($record)) {
      return FALSE;
    }
*/
    // Checks if already exists
    // TODO? here datafile is read twice -> bad performance if more data...
//    $id = array_key_first($record);
    if ($this->getById($id)) {
      return FALSE;
    }

    // Get existing data
    $arr = $this->getAll();

    // Remove old data, if limit reached 
    $count = count($arr);
    if ($count == $this->limit) {
      array_pop($arr);
    }

    // Add new record to the *end* of array
//    $id = array_key_first($record); 
    $arr[$id] = $record;

    // Save data
    $json = json_encode($arr);
    return file_put_contents($this->dataFile, $json); // overwrite
  }

}

// PHP 7 shim
// http://php.net/manual/en/function.array-key-first.php
if (!function_exists('array_key_first')) {
  /**
   * Gets the first key of an array
   *
   * @param array $array
   * @return mixed
   */
  function array_key_first(array $array)
  {
      return $array ? array_keys($array)[0] : null;
  }
}




