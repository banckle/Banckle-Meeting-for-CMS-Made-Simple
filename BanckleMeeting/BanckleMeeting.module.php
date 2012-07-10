<?php
/* Your initial Class declaration. This file's name must
   be "[class's name].module.php", or, in this case,
   Catlist.module.php
*/
error_reporting(E_ERROR);
ini_set('display_errors', '1');
class BanckleMeeting extends CMSModule
{
	function GetHelp($lang='en_US')
	{
		// Redirect to Help.
	}
	
	function getCurrentPageUrl(array $newparams = array(),$remove_others=false, array $remove_exceptions=array())
	{
		$pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
		if ($_SERVER["SERVER_PORT"] != "80")
		{
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} 
		else 
		{
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		
		$url_arr = parse_url($pageURL);
		$pageURL = $url_arr['scheme'] . "://" . $url_arr['host'] . $url_arr['path'];
		
		if(count($_SERVER['QUERY_STRING']) > 0 || count($newparams) > 0)
		{
			$pageURL .= "?";
		}
		
		if($remove_others == false)
		{
			if(count($_SERVER['QUERY_STRING']) > 0)
			{
				parse_str($_SERVER['QUERY_STRING'],$params);
			}
		}
		else
		{
			$param = array();
			
			if(count($remove_exceptions) > 0)
			{
				if(count($_SERVER['QUERY_STRING']) > 0)
				{
					parse_str($_SERVER['QUERY_STRING'],$params);
					
					foreach($params as $key => $param)
					{
						if(!in_array($key,$remove_exceptions))
						{
							unset($params[$key]);
						}
					}			
				}			
			}		
		}
		
		$params = array_merge($params,$newparams);
		
		foreach($params as $key => $param){
			if(empty($param)) unset($params[$key]);
		}
		
		$pageURL .= http_build_query($params,'','&');
		
		return $pageURL;
	}

    /*---------------------------------------------------------
       GetName()
       must return the exact class name of the module.
       If these do not match, bad things happen.

       This is the name that's shown in the main Modules
       page in the Admin.
      ---------------------------------------------------------*/
    function GetName()
    {
        return 'BanckleMeeting';
    }

    /*---------------------------------------------------------
       GetFriendlyName()
       This can return any string.
       This is the name that's shown in the Admin Menus and section pages
          (if the module has an admin component).
      ---------------------------------------------------------*/
    function GetFriendlyName()
    {
        return $this->Lang('friendlyname');
    }


    /*---------------------------------------------------------
       GetVersion()
       This can return any string, preferably a number or
       something that makes sense for designating a version.
       The CMS will use this to identify whether or not
       the installed version of the module is current, and
       the module will use it to figure out how to upgrade
       itself if requested.
      ---------------------------------------------------------*/
    function GetVersion()
    {
        return '0.1';
    }
         
         
    /*---------------------------------------------------------
         IsPluginModule()
         This function returns true or false, depending upon
         whether users can include the module in a page or
         template using a smarty tag of the form
         {cms_module module='Skeleton' param1=val param2=val...}
         If your module does not get included in pages or
         templates, return "false" here.
    
         (Required if you want to use the method DoAction later.)
         ---------------------------------------------------------*/
    function IsPluginModule()
    {
         return true;
    }
	
	function HasAdmin()
	{
	  // Return true or false depending on whether you actually
	  // want to add the admin page for your module to the admin menu.
	  return true;
	}
	
	function GetAdminSection()
	{
	  // Tells, which tab we want to put the menuitem of our module.
	  // Can be at least 'content', 'extensions' and 'usergroups' ,
	  // maybe others too.
	  return 'extensions';
	}
	
	function VisibleToAdminUser()
	{
	  // Depending on permissions, tell whether the menuitem 
	  // can be shown.
	  return true;
	}
	
	function Install()
	{

		//Get a reference to the database
		$db = cmsms()->GetDb();
		
		// mysql-specific, but ignored by other database
		$taboptarray = array('mysql' => 'TYPE=MyISAM');
		
		//Make a new "dictionary" (ADODB-speak for a table)
		$dict = NewDataDictionary($db);
		
		//Add the fields as a comma-separated string.
		// See the ADODB manual for a list of available field types.
		//In our case, the id is an integer, the name is a varchar(100) field,
		// the description is a text field, and the price is a float.
		$flds = "id I KEY AUTO,
				 name C(255),
				 value C(255)";
	
		//Tell ADODB to create the table called "module_catlist_products", 
		// using the our field descriptions from above.
		//Note the naming scheme that should be followed when adding tables to the database,
		// so as to make it easy to recognize who the table belongs to, and to avoid conflict with other modules.
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_bm_info', $flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);
				
		$sql = "UPDATE " . cms_db_prefix()."htmlblobs SET html = CONCAT(html,'{cms_module module=\'BanckleMeeting\'}') WHERE htmlblob_name = 'footer';";
		$db->Execute($sql);
				
		
		//Create a permission
		//The first argument is the name for the permission that will be used by the system.
		//The second argument is a more detailed explanation of the permission.
		$this->CreatePermission('BanckleMeeting Admin', 'Manage BanckleMeeting');
	}
	
	function InstallPostMessage()
	{
		return $this->Lang('postinstall');
	}
	
	function Uninstall()
	{
		//Get a reference to the database
		$db = cmsms()->GetDb();
	
		//Remove the database table
		$dict = NewDataDictionary( $db );
		$sqlarray = $dict->DropTableSQL( cms_db_prefix().'module_bm_info' );
		$dict->ExecuteSQLArray($sqlarray);
		
		
		$html = str_ireplace("","",$row['html']);
		$sql = "UPDATE " . cms_db_prefix()."htmlblobs SET html = REPLACE(html,'{cms_module module=\'BanckleMeeting\'}','') WHERE htmlblob_name = 'footer';";
		$dbresult =& $db->Execute($sql);
		
	
		//Remove the permission
		$this->RemovePermission('BanckleMeeting Admin');
	}
	
	function UninstallPreMessage()
	{
		return $this->Lang('uninstall_confirm');
	}
	
	function UninstallPostMessage()
	{
		return $this->Lang('postuninstall');
	}
	
	
	function DoAction($action, $id, $params, $returnid=-1)
	{
		if ($action == 'default')
		{
			$db =& $this->GetDb();
			$sql = "SELECT * FROM " . cms_db_prefix()."module_bm_info WHERE name = 'widget_code'";
			$dbresult =& $db->Execute($sql);
			$row = $dbresult->FetchRow();
			
			if(!empty($row))
			{
				$widget = $row['value'];
			}
			else
			{
				$widget = "";
			}
			
			if($widget != "")
			{
				preg_match('|<iframe [^>]*(src="[^"]+")[^>]*|', $widget, $matches);
				$url = substr(rtrim($matches[1],'"'),5);
				
				
				$contents = file_get_contents($url);
				$content_arr = json_decode($contents);
				
				if(isset($content_arr->error))
				{
					$widget = "";
				}
			}
			
			$this->smarty->assign('widget', $widget);
			// Display the populated template
			echo $this->ProcessTemplate('widget.tpl');
		}
		if ($action == 'defaultadmin')
		{
			$db =& $this->GetDb();
			$sql = "SELECT * FROM " . cms_db_prefix()."module_bm_info WHERE name = 'widget_code'";
			$dbresult =& $db->Execute($sql);
			$row = $dbresult->FetchRow();
			
			$list = array();
			
			if(empty($row))
			{
				$data['active'] = 0;
				$widget_code = '';
			}
			else
			{
				$data['active'] = 1;
				$widget_code = $row['value'];
			}
			
			$data['widget_code'] = $widget_code;
			
			if(isset($_REQUEST['panel']) && !empty($_REQUEST['panel']))
			{			
				$data['panel'] = $_REQUEST['panel'];
			}
			else
			{
				$data['panel'] = "default";
			}
			
			$data['current_url'] = $this->getCurrentPageUrl();
			$data['dashboard_url'] = $this->getCurrentPageUrl(array('panel'=>''));
			
			if(isset($_POST['widget_code']) && !empty($_POST['widget_code']))
			{								
				
				if(isset($_POST['deactivate'])) $widget_code = "";
				else
				$widget_code = $_POST['widget_code'];
				
				
				if(empty($widget_code)){
					$sql = "DELETE FROM " . cms_db_prefix()."module_bm_info WHERE name = 'widget_code'";
					$data['active'] = 0;
				}
				else
				{
					$sql = "INSERT INTO " . cms_db_prefix()."module_bm_info SET name = 'widget_code', value='". mysql_real_escape_string($widget_code)."'";
					$data['active'] = 1;
				}				
				
				$dbresult =& $db->Execute($sql);
				$data['panel'] = "default";
				
			}			
			
			$this->smarty->assign('data',$data);
			echo $this->ProcessTemplate('admin.tpl');
									
		}
	}
	
}
?>