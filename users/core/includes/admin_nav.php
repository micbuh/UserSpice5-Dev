<?php
/*
UserSpice 4
An Open Source PHP User Management System
by the UserSpice Team at http://UserSpice.com
*/

/*
Load main navigation menus
*/
if (!isset($db)) {
    $db = DB::getInstance();
}
$sql = "SELECT menus.id, menu_title, parent, dropdown, logged_in,
            display_order, label, icon_class,
            CONCAT(IF(link <> '', link, pages.page), link_args) AS link
        FROM ".$GLOBALS['T']['menus']." menus
        LEFT JOIN ".$GLOBALS['T']['pages']." pages ON (menus.page_id = pages.id)
        WHERE menu_title='admin'
        ORDER BY display_order";
$admin_nav_all = $db->query($sql);

/*
Set "results" to true to return associative array instead of object...part of db class
*/
$admin_nav=$admin_nav_all->results(true);

/*
Make menu tree
*/
$prep=prepareMenuTree($admin_nav);

?>

<nav class="navbar navbar-default">
<div class="container-fluid">
  <div class="navbar-header">
	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar_admin" aria-expanded="false" aria-controls="navbar">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	</button>
  </div>
  <div id="navbar_admin" class="navbar-collapse collapse">
	<ul class="nav navbar-nav navbar-left">
<?php
foreach ($prep as $key => $value){
	/*
	Check if there are children of the current nav item...if no children, display single menu item, if children display dropdown menu
	*/
	if(sizeof($value['children'])==0){
		if ($GLOBALS['user']->isLoggedIn()){
			if (checkMenu($value['id'],$GLOBALS['user']->data()->id) && $value['logged_in']==1){
				echo prepareItemString($value);
			}
		}else{
			if ($value['logged_in']==0 || checkMenu($value['id'])){
				echo prepareItemString($value);
			}
		}
	}else{
		if ($GLOBALS['user']->isLoggedIn()){
			if (checkMenu($value['id'],$GLOBALS['user']->data()->id) && $value['logged_in']==1){
				$dropdownString=prepareDropdownString($value);
				$dropdownString=str_replace('{{username}}',$GLOBALS['user']->data()->username,$dropdownString);
				echo $dropdownString;
			}
		}else{
			if ($value['logged_in']==0 || checkMenu($value['id'])){
				$dropdownString=prepareDropdownString($value);
				$dropdownString=str_replace('{{username}}',$GLOBALS['user']->data()->username,$dropdownString);
				echo $dropdownString;
			}
		}
	}
}
?>
	</ul>
  </div><!--/.nav-collapse -->
</div><!--/.container-fluid -->
</nav>