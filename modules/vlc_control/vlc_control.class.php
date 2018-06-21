<?
/**
* Blank
*
*
* @package project
* @author Serge J. <jey@tut.by>
* @copyright http://www.smartliving.ru/ (c)
*/
//
//
class vlc_control extends module {
/**
* vlc_control
*
* Module class constructor
*
* @access private
*/
function vlc_control() {
  $this->name="vlc_control";
  $this->title="VLC remote control";
  $this->module_category="<#LANG_SECTION_APPLICATIONS#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data = 1) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  if ($this->single_rec) {
   $out['SINGLE_REC']=1;
  }
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
}
/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($parent_name = "") {
   $className = 'vlc_control';
   $objectName = 'VlcControl';
   $methodName = 'Control';
   $properties = array('LastPlayed', 'VolumeLevel', 'PlayTerminal', 'On');
   $code = 'include_once(DIR_MODULES.\'vlc_control/vlc_control.class.php\');
      $vlc_control=new vlc_control();

      if(is_array($params))
      {
        if(isset($params[\'track\'])) $vlc_control->change_track($params[\'track\'],$vlc_control);
        if(isset($params[\'cmd\'])) $vlc_control->control($params[\'cmd\']);
        if(isset($params[\'vol\'])) $vlc_control->set_volume($params[\'vol\'],$vlc_control);
      }
      else
      {
        if($params==\'play\' || $params==\'stop\')  $vlc_control->control($params);
        else if(strpos($params, "vol")===0) $vlc_control->set_volume((int)substr($params,3),$vlc_control);
        else if(strpos($params, "sta:")===0) $vlc_control->change_track(substr($params,4),$vlc_control);
      }';

      $rec = SQLSelectOne("SELECT ID FROM classes WHERE TITLE LIKE '" . DBSafe($className) . "'");
      if (!$rec['ID']) {
          $rec = array();
          $rec['TITLE'] = $className;
          //$rec['PARENT_LIST']='0';
          $rec['DESCRIPTION'] = 'VLC Media Player remote control';
          $rec['ID'] = SQLInsert('classes', $rec);

      }

      $obj_rec = SQLSelectOne("SELECT ID FROM objects WHERE CLASS_ID='" . $rec['ID'] . "' AND TITLE LIKE '" . DBSafe($objectName) . "'");
      if (!$obj_rec['ID']) {
          $obj_rec = array();
          $obj_rec['CLASS_ID'] = $rec['ID'];
          $obj_rec['TITLE'] = $objectName;
          $obj_rec['DESCRIPTION'] = 'Settings';
          $obj_rec['ID'] = SQLInsert('objects', $obj_rec);
      }

      $metod_rec = SQLSelectOne("SELECT ID FROM methods WHERE OBJECT_ID='" . $obj_rec['ID'] . "' AND TITLE LIKE '" . DBSafe($metodName) . "'");
      if (!$metod_rec['ID']) {
        $metod_rec = array();
        $metod_rec['OBJECT_ID'] = $obj_rec['ID'];
        $metod_rec['CLASS_ID'] = 0;
        $metod_rec['TITLE'] = $metodName;
        $metod_rec['DESCRIPTION'] = '';
        $metod_rec['CODE'] = $code;
        $metod_rec['ID'] = SQLInsert('methods', $metod_rec);
      }
      else
      {
        $metod_rec['CODE'] = $code;
        SQLUpdate('methods', $metod_rec);
      }

      for ($i = 0; $i < count($propertis); $i++) {
          $prop_rec = SQLSelectOne("SELECT ID FROM properties WHERE OBJECT_ID='" . $obj_rec['ID'] . "' AND TITLE LIKE '" . DBSafe($propertis[$i]) . "'");
          if (!$prop_rec['ID']) {
              $prop_rec = array();
              $prop_rec['TITLE'] = $propertis[$i];
              $prop_rec['OBJECT_ID'] = $obj_rec['ID'];
              $prop_rec['ID'] = SQLInsert('properties', $prop_rec);
          }
      }

  parent::install($parent_name);
 }

 function uninstall()
 {
   SQLExec("drop table if exists vlc_control");
 }

 function dbInstall($data)
 {

$data = <<<EOD
 vlc_control: ID int(10) unsigned NOT NULL auto_increment
 vlc_control: stations text
 vlc_control: name text
EOD;
        parent::dbInstall($data);
}

// --------------------------------------------------------------------
}
?>
