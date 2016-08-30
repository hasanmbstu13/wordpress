<?php
/*
Contributor:       Zeeshan Khan
Plugin Name:       Show WordPress Queries - Query Interface
Plugin URI:        http://www.queryinn.com/
Description:       An interface to show, run & execute wordpress queries, display queries on pages with loading time so you may optimize them etc.
Author:            Zeeshan Khan
Author URI:        http://www.queryinn.com/index.php/about/
Version:           1.3.1
*/
// admin check
if(!function_exists('is_admin'))
{
	die();
}
session_start();
$config["site_name"] = "http://www.queryinn.com/";	
if(intval($_SESSION["show_queries"]) == 1)
{
	if ( ! defined( 'QUERY_CACHE_TYPE_OFF' ) )
		define( 'QUERY_CACHE_TYPE_OFF', TRUE );

	if ( ! defined( 'SAVEQUERIES' ) )
		define( 'SAVEQUERIES', TRUE );
	
	add_action('wp_head', 'show_buton');
	add_action('wp_footer', 'show_queries');
	add_action('wp_login', 'logout_session');
	add_action('wp_logout', 'logout_session');
}

add_action('admin_menu', 'queryinterface_admin_menu');

function logout_session()
{
	unset($_SESSION["show_queries"]);
}

function queryinterface_admin_menu()
{

	add_object_page('Show WordPress Queries', 'Show WordPress Queries', 'administrator', 'query-interface', 'queryinterface_func');
}

function show_buton()
{
	?>
	<script>
		function qi_scroll()
		{
			var divid = document.getElementById("qi_table");
			divid.scrollIntoView(true);
			return false;
		}
	</script>
	<style>
		.qi_top
		{
			font-family: Arial;
			font-size: 14px;
			text-align:right;
		}
		a.qi_top
		{
			color:#262626;
		}
		a.qi_top:hover
		{
			color:#000000;
		}
		.qi_normal 
		{
			border: 0px;
			margin: 1em auto;
			display:block;
			position:relative;
			z-index:999;
		}

	</style>
		<table width="100%">
			<tr>
				<td width="100%" class="qi_top qi_normal"><strong><a class="qi_top" href="javascript:void(0)" onClick="qi_scroll()">Click to view queries</a></td>
			</tr>
		</table>
	<?php
}

function show_queries()
{
	?>
	<style>
		.qi_normal 
		{
			border: 0px;
			margin: 1em auto;
			display:block;
			position:relative;
			z-index:999;
		}

		.qi_alt1, .qi_head 
		{
			font-family: Arial;
			font-size: 14px;
			background-color: #F0F0F0;
			color: #262626;
			margin: 1em auto;
			text-align: left;
			width: 90%;
			z-index: 999;
		}
		.qi_alt1 td
		{
			padding: 5px 5px;
			display:block;
			position:relative;
			z-index:999;
			
		}
		a.qi_head
		{
			color:#262626;
		}
		a.qi_head:hover
		{
			color:#000000;
		}
		.qi_alt1 td:hover
		{
			background-color: #D0DAFD;
			color: #000000;
		}
	</style>
	<?php
	global $current_user, $config, $wpdb;
	if($wpdb->queries)
	{
		?>
			<table id="qi_table" class="qi_normal">
				<tr>
					<td>
						<table class="qi_head">
							<tr>
								<td style="text-align: center;">
									<a class="qi_head" target="_blank" href="http://wordpress.org/plugins/query-interface/"><strong>Query Interface</strong></a> by 
									<a class="qi_head" target="_blank" href="<?php echo $config["site_name"];?>"><strong>Zeeshan Khan</strong></a>
								</td>
							</tr>
							<tr>
								<td>- Use our free tool to optimize your queries by grabbbing the following queries one by one, and paste them on the 
								Query Interface plugin on admin area by clicking on Check Query button.<br>
								- If you want us to optimize your slow queries, 
								<a class="qi_head" target="_blank" href="<?php echo $config["site_name"];?>index.php/contact/?track=wp_qi"><strong>give us a contact</strong></a> 
								and we'll help you finding and resolving your performance and optimization issues in affordable rates.
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<?php
				$i = 0;
				foreach ($wpdb->queries as $qry)
				{
					?>
						<tr>
							<td>
								<table class="qi_normal qi_alt1">
									<tr>
										<td><strong>Query</strong></td>
										<td><?php echo $qry[0]; ?></td>
									</tr>
									<tr>
										<td><strong>Time</strong></td>
										<td><?php echo $qry[1]; ?> seconds</td>
									</tr>
									<tr>
										<td><strong>File(s)</strong></td>
										<td><?php echo $qry[2]; ?></td>
									</tr>
								</table>
							</td>
						</tr>
				<?php
				}
				?>
			</table>
				<?php
	}
}

function view_fields(){
	global $wpdb;
	$table = $_GET["val"];
	$sql = "SHOW FIELDS FROM ".$table;
	$rs = mysql_query($sql) or die(mysql_error());
	$fields = array();
	while($row = mysql_fetch_array($rs))
	{
		$fields[] = $row[0];
	}
?>
	<div class="td_head">Fields</div>
	<div>
		<select name="fields" id="fields" multiple="true" style='height: 150px;'>
			<?php foreach($fields as $fld) { ?>
				<option value="<?php echo $fld?>"><?php echo $fld?></option>
			<?php } ?>
		</select>
	</div>
<?php

exit;
}

function set_queries($val)
{
	session_start();
	$show_queries = intval($_SESSION["show_queries"]);
	if($show_queries == 1)
	{
		$_SESSION["show_queries"] = 0;
		$act = "Activate";
	}
	else
	{
		$_SESSION["show_queries"] = 1;
		$act = "Deactivate";
	}
	echo "<strong>".$act."</strong>";
	exit;
}

add_action( 'wp_ajax_view_fields', 'view_fields' );
add_action( 'wp_ajax_nopriv_view_fields', 'view_fields' );

add_action( 'wp_ajax_set_queries', 'set_queries' );
add_action( 'wp_ajax_nopriv_set_queries', 'set_queries' );


function queryinterface_func()
{
	global $config;
	include("qi.php");
}