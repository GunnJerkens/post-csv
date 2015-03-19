<?php

ini_set('auto_detect_line_endings', true);
require_once(__DIR__.'/config.php');

// Gonna write this quick and janky. Sorry.
class PostForms {

  /**
   * Private class vars
   * @var $config object
   * @var $array array
   * @var $pairs array
   * @var $labels array
   */
  private $config, $array, $pairs, $labels;

  /**
   * Constructor
   */
  function __construct()
  {
    $this->setConfig();
    $this->setDefaults();
    $state = $this->importCsv();
    if($state) {
      $this->filterLabels();
      $this->combineLabels();

      foreach($this->pairs as $data) {
        $string = $this->buildPostString($data);
        $curl   = $this->postForm($string);
      }

      exit('I did stuff, go check. I can wait.');

    } else {
      exit('Failed to open csv, do better.');
    }
  }

  /**
   * Sets our config
   *
   * @return void
   */
  private function setConfig()
  {
    global $config;
    $this->config = (object) $config;
  }

  /**
   * Set our private class vars to empty arrays
   *
   * @return void
   */
  private function setDefaults()
  {
    $this->arr    = array();
    $this->pairs  = array();
    $this->labels = array();
  }

  /**
   * Import the data.csv into $array
   *
   * @return void
   */
  private function importCsv()
  {
    if(($handle = fopen($this->config->filepath, "r")) !== false) {

      while (($data = fgetcsv($handle, ",")) !== false) {
        array_push($this->arr, $data);
      }
      fclose($handle);
      return true;
    } else {
      return false;
    }
  }

  /**
   * Filter off labels from the first row
   *
   * @return void
   */
  private function filterLabels()
  {
    foreach ($this->arr[0] as $key=>$value) {
      $this->labels[$value] = trim(strtolower(str_replace(' ', '_', $value)));
    }
    unset($this->arr[0]);
  }

  /**
   * Take each array and combine it with the labels
   *
   * @return void
   */
  private function combineLabels()
  {
    foreach($this->arr as $key=>$value) {
      $this->pairs[$key] = array_combine($this->labels, $value);
    }
  }

  /**
   * Create a string for the POST
   *
   * @param array
   *
   * @return string
   */
  private function buildPostString($data)
  {
    if (sizeof($this->config->append > 0)) {
      foreach($this->config->append as $key=>$value) {
        $data[$key] = $value;
      }
    }
    foreach($data as $key => $value) {
      if (is_array($value)){
        $value = implode(", ", $value);
      }
      $post_items[] = $key . "=" . urlencode($value);
    }
    $post_string = implode("&", $post_items);

    return $post_string;
  }

  /**
   * Post to the endpoint
   *
   * @param string
   *
   * @return 
   */
  private function postForm($string)
  {
    $curl = curl_init($this->config->url);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1; rv:6.0.2) Gecko/20100101 Firefox/6.0.2");
    curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_HEADER, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $string);

    $result = curl_exec($curl);
    $info   = curl_getinfo($curl);
    curl_close($curl);

    return $result;
  }

}
new PostForms();


