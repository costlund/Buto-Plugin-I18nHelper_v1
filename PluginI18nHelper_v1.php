<?php
/**
 * i18n Helper.
 */
class PluginI18nHelper_v1{
  private $settings = null;
  function __construct($buto) {
    if($buto){
      wfPlugin::includeonce('wf/array');
      $this->settings = new PluginWfArray(wfArray::get($GLOBALS, 'sys/settings'));
    }
  }
  public function page_start(){
    $languages = $this->settings->get('i18n/languages');
    
    $data = new PluginWfArray();
    foreach ($languages as $key => $value) {
      $data->set("languages/$value/name", $value);
    }
    
    $element = array();
    
    $language_keys = array();
    foreach ($languages as $key => $language) {
      //$element[] = wfDocument::createHtmlElement('h1', $language);
      /**
       * Check from theme.
       */
      $filename = '/theme/[theme]/i18n/'.$language.'.yml';
      if(wfFilesystem::fileExist(wfArray::get($GLOBALS, 'sys/app_dir').$filename)){
        $temp = wfSettings::getSettings($filename);
        foreach ($temp as $key2 => $value2) {
          $language_keys[$key2] = $key2;
        }
        //$element[] = wfDocument::createHtmlElement('pre', wfHelp::getYmlDump($temp));
        $data->set("languages/$language/item", $temp);
      }else{
        $data->set("languages/$language/item", array());
      }
    }
    $data->set('keys', $language_keys);
    
    
    /**
     * Sort.
     */
    ksort($language_keys);
    
    
    
    /**
     * Create table.
     */
    $tr = array();
    $th = array();
    $th[] = wfDocument::createHtmlElement('td', 'Key');
    foreach ($languages as $key => $value) {
      $th[] = wfDocument::createHtmlElement('td', $value);
    }
    $tr[] = wfDocument::createHtmlElement('tr', $th);
    foreach ($language_keys as $key => $value) {
      $td = array();
      $td[] = wfDocument::createHtmlElement('td', '<!---->'.$key);
      foreach ($languages as $key2 => $value2) {
        $td[] = wfDocument::createHtmlElement('td', $data->get("languages/$value2/item/$key"));
        //$td[] = wfDocument::createHtmlElement('td', "languages/$value2/item/$key");
      }
      $tr[] = wfDocument::createHtmlElement('tr', $td);
    }
    $element[] = wfDocument::createHtmlElement('table', $tr);
    
    
    
    
    /**
     * String to copy in textarea.
     */
    $str = null;
    foreach ($language_keys as $key => $value) {
      $str .= $key."\n";
    }
    
    
    $element[] = wfDocument::createHtmlElement('form', array(
      wfDocument::createHtmlElement('textarea', $str, array('name' => 'keys')),
      wfDocument::createHtmlElement('textarea', null, array('name' => 'word')),
      wfDocument::createHtmlElement('input', null, array('type' => 'submit', 'value' => 'Render yml'))
    ), array('target' => '_blank', 'action' => 'render', 'method' => 'post'));
    $element[] = wfDocument::createHtmlElement('style', 'textarea{width:100%;height:300px}');
    wfDocument::renderElement($element);
  }
  public function page_render(){
    $keys = explode("\n", wfRequest::get('keys'));    
    $word = explode("\n", wfRequest::get('word'));    
    $yml = null;
    foreach ($keys as $key => $value) {
      $value = wfPhpfunc::str_replace("\r", "", $value);
      $yml .= "'".$value."': '".str_replace("\r", "", $word[$key])."'\n";
    }
    $element = array();
    $element[] = wfDocument::createHtmlElement('textarea', $yml);
    $element[] = wfDocument::createHtmlElement('style', 'textarea{width:100%;height:300px}');
    wfDocument::renderElement($element);
    
  }
}
























