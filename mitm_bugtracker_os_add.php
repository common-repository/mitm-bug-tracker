<?php
if (isset($_REQUEST['os_id']) && $_REQUEST['os_id']) {
  $_result = $this->wpdb->get_results("SELECT * FROM {$this->wpdb->prefix}mitm_os WHERE os_id='{$_REQUEST['os_id']}'");
  $_result = $_result[0];
  
  $id = esc_attr($_result->os_id);
  $os_name = esc_attr($_result->os_name);
}
?>

<div class="wrap">
  <div id="icon-options-general" class="icon32 icon32-posts-post"><br>
  </div>
  <h2>Save Operating System</h2>
  <div id="message" class="below-h2"></div>
  <form name="os_add" method="post" action="" enctype="multipart/form-data">
    <?php 
    if (isset($_REQUEST['os_id']) && $_REQUEST['os_id']) {
      echo '<input type="hidden" name="os_id" value="' . esc_attr($_REQUEST['os_id']) . '" />';
    }
    ?>
    <table class="form-table">
      <tbody>
        <tr>
          <th><label for="os_name">Operating System Name</label></th>
          <td><input name="os_name" type="text" id="os_name" value="<?php echo $os_name; ?>" class="regular-text required" /></td>
        </tr>
      </tbody>
    </table>
    <p class="submit">
      <input type="submit" name="submit" id="submit" class="button-primary" value="Save Operating System">
      <a href="admin.php?page=mitm_bugtracker_os_view" class="button-secondary">Cancel</a> </p>
  </form>
</div>
<style>
  tr.error th, tr.error td {background-color: #FFEBE8!important; border-bottom: solid 1px #CCC!important;}
  tr.error td .required {border-color: #C00!important;}
</style>
