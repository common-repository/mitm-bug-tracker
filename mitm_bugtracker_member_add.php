<?php
if (isset($_REQUEST['member_id']) && $_REQUEST['member_id']) {
  $_result = $this->wpdb->get_results("SELECT * FROM {$this->wpdb->prefix}mitm_member WHERE member_id='{$_REQUEST['member_id']}'");
  $_result = $_result[0];
  $member_name = esc_attr($_result->member_name);
}
?>

<div class="wrap">
  <div id="icon-options-general" class="icon32 icon32-posts-post"><br>
  </div>
  <h2>Save Member</h2>
  <div id="message" class="below-h2"></div>
  <form name="member_add" method="post" action="" enctype="multipart/form-data">
    <?php 
    if (isset($_REQUEST['member_id']) && $_REQUEST['member_id']) {
      echo '<input type="hidden" name="member_id" value="' . esc_attr($_REQUEST['member_id']) . '" />';
    }
    ?>
    <table class="form-table">
      <tbody>
        <tr>
          <th><label for="member_name">Member Name</label></th>
          <td><input name="member_name" type="text" id="member_name" value="<?php echo $member_name; ?>" class="regular-text required" /></td>
        </tr>
      </tbody>
    </table>
    <p class="submit">
      <input type="submit" name="submit" id="submit" class="button-primary" value="Save Member">
      <a href="admin.php?page=mitm_bugtracker_member_view" class="button-secondary">Cancel</a> </p>
  </form>
</div>
<style>
  tr.error th, tr.error td {background-color: #FFEBE8!important; border-bottom: solid 1px #CCC!important;}
  tr.error td .required {border-color: #C00!important;}
</style>
