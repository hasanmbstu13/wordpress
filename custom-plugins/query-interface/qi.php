<?php
session_start();
// admin check
if(!function_exists('is_admin'))
{
	die();
}
?>

<style type="text/css">
	.td_head {
        background-color: #DFDFDF;
		font-weight: bold;
      }
</style>
<script type="text/javascript">							 
function embed_value(val)
{
	jQuery.ajax({
		type: 'GET',
		dataType: 'html',
		url: ajaxurl,
		data: {"action": "view_fields", "val" : val},
		success: function(data)
		{
			jQuery('#fields_div').html(data);
		}
	});
	return false;
}

function set_show_queries()
{
	jQuery.ajax({
		type: 'GET',
		dataType: 'html',
		url: ajaxurl,
		data: {"action": "set_queries"},
		success: function(data)
		{
			jQuery('#act_deact').html(data);
		}
	});
	return false;
}


function check_query(btn_val)
{
	jQuery("#chk").val(btn_val);
	jQuery("#qi").submit();
}
<?php

function get_query($sql, $type=0)
{
	global $config;
	$results = array();
	if($type == 1)
	{
		$sql_explain = "EXPLAIN "; 
		$sql = $sql_explain.$sql;
	}
	elseif($type == 2)
	{
		$sql = "SELECT COMMAND as 'type', TIME as 'seconds', STATE as 'condition', INFO 'query'
				FROM INFORMATION_SCHEMA.PROCESSLIST
				WHERE INFO NOT LIKE '%INFORMATION_SCHEMA%'";
	}
	elseif($type == 3)
	{
		$sql_index = "SHOW INDEX FROM ";
		$sql = $sql_index.$sql;	
	}
	$rs = mysql_query($sql);
	$results["total_rows"] = mysql_num_rows($rs);
	$results["num_fields"] = mysql_num_fields($rs);
	for($i=0; $i<$results["num_fields"]; $i++)
		$results["fields"][] = mysql_field_name($rs, $i);

	if(mysql_error())
	{
		echo mysql_error();
	}
	else
	{
		while($row = mysql_fetch_assoc($rs))
		{
			$results["values"][] = $row;
		}
	}
	return $results;
}

$sql = "SHOW TABLES";
$rs = mysql_query($sql);
$tables = array();
while($row = mysql_fetch_array($rs))
{
	$tables[] = $row[0];
}

// slash n quote issue
$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
while (list($key, $val) = each($process)) 
{
	foreach ($val as $k => $v) 
	{
		unset($process[$key][$k]);
		if (is_array($v)) 
			{
			$process[$key][stripslashes($k)] = $v;
			$process[] = &$process[$key][stripslashes($k)];
			} 
		else 
		{
			$process[$key][stripslashes($k)] = stripslashes($v);
		}
	}
}
unset($process);
?>
</script>
<form name="qi" id="qi" method="post">
	<table width="100%" align="left">
		<tr>
			<td>
				<table>
					<tr>
						<td colspan="3" align="center" class="">Click here to 
								<a href="javascript:void(0)" onclick="set_show_queries()"><span id="act_deact">
								<strong><?php echo $_SESSION["show_queries"] == 1 ? "Deactivate" : "Activate" ?></strong></span></a>
								the Queries to Display on the Website (for admins only)
						</td>
					</tr>
					<tr>
						<td align="center" class="td_head">Tables</td>
						<td align="center" class="td_head">Query</td>
					</tr>
					<tr>
						<td valign="top">
							<select name="tables" id="tables" multiple="true" ondblclick="embed_value(this.value)" style='height: 150px;'>
								<?php foreach($tables as $tbl)
								{
									$selected = "";
									if(isset($_POST["tables"]) && $_POST["tables"] == $tbl)
									{
										$selected = " selected = 'selected' ";
									}
								?>
									<option value="<?php echo $tbl?>" <?php echo $selected?>><?php echo $tbl?></option>
								<?php } ?>
							</select>
							<br>
							<input type="hidden" id="chk" name="chk" value="0" />
							<div width="100%"><table width="100%"><tr><br><td width="100%" id="fields_div"></td></tr></table>	</div> 
						</td>
						<td>
							<div>
								<textarea style="width: 450px; height: 300px;" id="query" name="query"><?php echo $_POST["query"]?></textarea>
							</div>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>
							<input name="submit_button" type="submit" value="Submit" />
							<input type="button" value="Check Query" onclick="javascript:check_query(1);" />
							<input type="button" value="Running Queries" onclick="javascript:check_query(2);" />
							<input type="button" value="Display Indexes" onclick="javascript:check_query(3);" />
						</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
				</table>
			</td>
			<td width="20">&nbsp;</td>
			<td valign="top">
				<table>
					<tr>
						<td class="td_head">About</td>
					</tr>
					<tr>
						<td>
							If you liked the plugin and was useful to your site, please consider liking and giving rating and your comments.<br><br>
							Also, if your website is <strong>loading slower</strong> as compared to your <strong>competitors</strong>, we'll try to make your <strong>queries</strong> and other part of website <strong>optimized</strong> in a manner to <strong>perform faster</strong>.<br><br>
							<a target="_blank" href="<?php echo $config["site_name"]?>index.php/contact/?track=wp_qi"><strong>Give us a contact</strong></a> and we'll help you finding and <strong>resolving your performance and optimization issues</strong>.<br><br>
							For further details on Performance and Optimization, <a target="_blank" href="<?php echo $config["site_name"]?>index.php/2012/01/code-query-optimizer-consultant-website-performance-optimization/?track=wp_qi"><strong>click here</strong></a>.
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</form>
<?php
if($_POST)
{
	$sql = "";
	$sql = $_POST["query"];
	$action = intval($_POST["chk"]);
	$results = get_query($sql, $action);
	if(mysql_error())
	{
		echo mysql_error();
	}
	else
	{
		?> <table align="center" border=1>
			<tr>
			<?php
			foreach($results["fields"] as $fld)
			{
				?>
					<td align="center"><strong><?php echo $fld?></strong></td>
				<?php
			}
			?> </tr> <?php
		$total_rows = $results["total_rows"];
		if($results["total_rows"] > 0)
		{
			foreach($results["values"] as $result_rows)
			{
			?>
				<tr>
			<?php
				foreach($result_rows as $result_cols)
				{
					?>
						<td align="center"><?php echo $result_cols?></td>
				<?php } ?>
				</tr>
			<?php
			}
		}
	?>
		<tr>
			<td colspan="<?php echo $results["num_fields"]?>"><strong>Total rows: <strong><?php echo $results["total_rows"]?></td>
		</tr>
	<?php
	}
}
?>
</table>
