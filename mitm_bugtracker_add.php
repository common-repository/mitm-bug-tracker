<?php
if (isset($_REQUEST['id']) && $_REQUEST['id']) {
	$table_name = $this->wpdb->prefix . "mitm_bug_tracker"; 
	$_result = $this->wpdb->get_results("SELECT * FROM $table_name WHERE bt_id='{$_REQUEST['id']}'");
	$_result = $_result[0];
	
	$bt_id 			  = esc_attr($_result->bt_id);
	$bt_project_name  = esc_attr($_result->bt_projectname);
	$bt_url 		  = esc_attr($_result->bt_url);
	$bt_date 		  = esc_attr($_result->bt_date);
	$bt_desc 		  = esc_attr(trim($_result->bt_desc));
	$bt_os 			  = esc_attr($_result->bt_os);
	$bt_browser 	  = esc_attr($_result->bt_browser);
	$bt_assignto 	  = esc_attr($_result->bt_assingto);
	$bt_priority 	  = esc_attr($_result->bt_priority);
	$bt_fixed 		  = esc_attr($_result->bt_fixed);
}
$table_name  = $this->wpdb->prefix . "mitm_os"; 
$oslist 	 = $this->wpdb->get_results("SELECT * FROM $table_name");
$table_name  = $this->wpdb->prefix . "mitm_browser"; 
$browserlist = $this->wpdb->get_results("SELECT * FROM $table_name");
$table_name  = $this->wpdb->prefix . "mitm_member"; 
$memberlist  = $this->wpdb->get_results("SELECT * FROM $table_name");
?>

<div class="wrap">
  <div id="icon-options-general" class="icon32 icon32-posts-post"><br>
  </div>
  <h2>Save New Bug</h2>
  <div id="message" class="below-h2"></div>
  <form name="frm_mitm_bug_tracker" id="frm_mitm_bug_tracker" enctype="multipart/form-data" action="" method="post">
    <input type="hidden" name="action" id="action" value="mitm_bugtracker_save" />
    <?php
if (isset($_REQUEST['id']) && $_REQUEST['id']) {
  echo '<input type="hidden" name="id" value="' . $_REQUEST['id'] . '" />';
}
?>
    <table class="form-table" style="width:auto;">
      <tr>
        <td align="left" colspan="2" style="color:#FF0000;">All fields are mandatory.</td>
      </tr>
      <tr>
        <td class="toppadding">Project Name:</td>
        <td class="bottompadding"><input type="text" name="project_name" id="project_name" value="<?php echo $bt_project_name;  ?>" /></td>
      </tr>
      <tr>
        <td class="toppadding">URL:</td>
        <td class="bottompadding"><input type="text" name="url" id="url" value="<?php echo $bt_url; ?>" /></td>
      </tr>
     
      <tr>
        <td class="toppadding">Description:</td>
        <td class="bottompadding">
			<textarea cols="25" rows="2" name="desc" id="desc"><?php echo $bt_desc; ?></textarea>
		</td>
      </tr>
      <tr>
        <td class="toppadding">Operating System:</td>
        <td class="bottompadding"><select name="os" id="os" style="width:140px;">
            <option value="">-</option>
            <?php
		  	if(count($oslist)>0) { 
				foreach($oslist as $os) { 
			?>
            <option value="<?php echo $os->os_id; ?>" <?php if($os->os_id==$bt_os) { ?> selected="selected" <?php } ?>><?php echo $os->os_name; ?></option>
            <?php
				}
			}
		  ?>
          </select>
        </td>
      </tr>
      <tr>
        <td class="toppadding">Browser:</td>
        <td class="bottompadding"><select name="browser" id="browser" style="width:140px;">
            <option value="">-</option>
            <?php
		  	if(count($browserlist)>0) { 
				foreach($browserlist as $browser) { 
			?>
            <option value="<?php echo $browser->browser_id; ?>" <?php if($browser->browser_id==$bt_browser) { ?> selected="selected" <?php } ?>><?php echo $browser->browser_name; ?></option>
            <?php
				}
			}
		  ?>
          </select>
        </td>
      </tr>
      <tr>
        <td class="toppadding">Assign To: </td>
        <td class="bottompadding"><select name="assingto" id="assingto" style="width:140px;">
            <option value="">-</option>
            <?php
		  	if(count($memberlist)>0) { 
				foreach($memberlist as $member) { 
			?>
            <option value="<?php echo $member->member_id; ?>" <?php if($member->member_id==$bt_assignto) { ?> selected="selected" <?php } ?>><?php echo $member->member_name; ?></option>
            <?php
				}
			}
		  ?>
          </select>
        </td>
      </tr>
      <tr>
        <td class="toppadding">Priority: <?php echo $bt_priority; ?></td>
        <td class="bottompadding"><select name="priority" id="priority" style="width:140px;">
            <option value="">-</option>
            <option value="High" <?php if($bt_priority=="High") { ?> selected="selected" <?php } ?>>High</option>
            <option value="Medium" <?php if($bt_priority=="Medium") { ?> selected="selected" <?php } ?>>Medium</option>
            <option value="Low" <?php if($bt_priority=="Low") { ?> selected="selected" <?php } ?>>Low</option>
          </select>
        </td>
      </tr>
      <tr>
        <td class="toppadding">Fixed? </td>
        <td class="bottompadding"><input type="radio" name="fixed" value="Yes" <?php if($bt_fixed == 'Yes') { ?> checked="checked" <?php } ?> id="fixed"/>
          &nbsp;Yes<br />
          <input type="radio" name="fixed" id="fixed" value="No" <?php if($bt_fixed == 'No') { ?> checked="checked" <?php } ?>/>
          &nbsp;No </td>
      </tr>
      <tr>
        <td colspan="2"><p class="submit">
            <input type="button" name="save" value="Save Bug" class="button-primary" onclick="ajaxSubmit();" />
            <a href="admin.php?page=mitm_bugtracker_view" class="button-secondary">Cancel</a> </p></td>
      </tr>
    </table>
  </form>
</div>
<style>
  tr.error th, tr.error td {background-color: #FFEBE8!important; border-bottom: solid 1px #CCC!important;}
  tr.error td .required {border-color: #C00!important;}
</style>
<script type="text/javascript">
function ajaxSubmit(){
	if(jQuery.trim(jQuery('#project_name').val()) == '') {
		alert('Please enter project name.');
		jQuery("body").scrollTop(jQuery("#project_name").offset().top);
		jQuery('#project_name').focus();
		return false;
	}
	if(jQuery.trim(jQuery('#url').val()) == '') {
		alert('Please enter url.');
		jQuery("body").scrollTop(jQuery("#url").offset().top);
		jQuery('#url').focus();
		return false;
	}
	if(jQuery.trim(jQuery('#desc').val()) == '') {
		alert('Please enter description.');
		jQuery("body").scrollTop(jQuery("#desc").offset().top);
		jQuery('#desc').focus();
		return false;
	}	
	if(jQuery.trim(jQuery('#os').val()) == '') {
		alert('Please enter operating system.');
		jQuery("body").scrollTop(jQuery("#os").offset().top);
		jQuery('#os').focus();
		return false;
	}	
	if(jQuery.trim(jQuery('#browser').val()) == '') {
		alert('Please enter browser.');
		jQuery("body").scrollTop(jQuery("#browser").offset().top);
		jQuery('#browser').focus();
		return false;
	}	
	if(jQuery.trim(jQuery('#assingto').val()) == '') {
		alert('Please enter assingto.');
		jQuery("body").scrollTop(jQuery("#assingto").offset().top);
		jQuery('#assingto').focus();
		return false;
	}	
	if(jQuery.trim(jQuery('#priority').val()) == '') {
		alert('Please enter priority.');
		jQuery("body").scrollTop(jQuery("#priority").offset().top);
		jQuery('#priority').focus();
		return false;
	}	
	if(jQuery('[name=fixed]:checked').length == 0) {
		alert('Please enter fixed.');
		jQuery("body").scrollTop(jQuery("#fixed").offset().top);
		jQuery('#fixed').focus();
		return false;
	}
	var bugtrackerfrm = jQuery('#frm_mitm_bug_tracker').serialize();
	jQuery.ajax({
		type: "POST",
		url: ajaxurl,
		data: bugtrackerfrm,
		mimeType:"multipart/form-data",
		dataType: 'json',
		success:function(data){
		  if(data.error) {
            jQuery("#message").show().addClass("error").removeClass('updated').html("<p>"+data.error+"</p>");
          } else if(data.msg) {
            jQuery("#message").removeClass('error')
            jQuery("#message").show().addClass('updated').html("<p>"+data.msg+"</p>");
		  }
		}
	});
	return false;
}
</script>
