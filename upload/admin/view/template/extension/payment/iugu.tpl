<!--
	Author: Valdeir Santana
	Site: http://www.valdeirsantana.com.br
	License: http://www.gnu.org/licenses/gpl-3.0.en.html
-->
<?php echo $header ?>
<style>
.live-chat:hover {
    background: #2ABB6B;
}
.live-chat {
    background: #48CC83;
    text-align:center;
}
article {
    position: relative;
    padding: 60px 30px;
    min-height: 280px;
    margin: 50px 0px 40px;
    color: #FFF;
    transition: all 0.3s ease;
    -moz-transition: all 0.3s ease;
    -webkit-transition: all 0.3s ease;
    -ms-transition: all 0.3s ease;
    -o-transition: all 0.3s ease;
}
article .title {
    position: relative;
}
article h2 {
    position: relative;
    display: inline-block;
    font-size: 32px;
}
article .title .fa {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 50px;
}
article p {
    position: relative;
    margin: 8px 0px 25px;
    line-height: 30px;
}
article .btn {
    position: relative;
    color: #FFF;
    font-size: 16px;
    letter-spacing: 0.05em;
    padding: 7px 56px;
    line-height: 30px;
    background-color: rgba(0, 0, 0, 0.15);
    border-radius: 22px;
}
article .overlay-link {
    position: absolute;
    left: 0px;
    top: 0px;
    display: block;
    width: 100%;
    height: 100%;
    z-index: 2;
}
#markdown {display:none}
.table-hover > tbody > tr:hover {
  background-color: #EEE;
}
</style>
<?php echo $column_left ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-iugu" class="btn btn-primary" data-toggle="tooltip" title="<?php echo $button_save?>"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel ?>" class="btn btn-info" data-toggle="tooltip" title="<?php echo $button_cancel ?>"><i class="fa fa-reply"></i></a>
      </div>
      
      <h1><?php echo $heading_title ?></h1>
      
      <ul class="breadcrumb">
        <?php foreach($breadcrumbs as $breadcrumb): ?>
        <li><a href="<?php echo $breadcrumb['href'] ?>"><?php echo $breadcrumb['text'] ?></a></li>
        <?php endforeach ?>
      </ul>
    </div>
  </div>
  
  <div class="container-fluid">
    
    <?php if($error_warning): ?>
    <div class="alert alert-danger">
      <?php echo $error_warning ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php endif ?>
    
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3><i class="fa fa-pencil"></i> <?php echo $text_edit ?></h3>
      </div>
      
      <div class="panel-body">
        <!-- Form -->
        <form action="<?php echo $action ?>" method="post" enctype="form-data/multipart" class="form form-horizontal" id="form-iugu">
          
          <!-- Nav -->
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-config" data-toggle="tab"><?php echo $tab_config ?></a></li>
            <li><a href="#tab-reminder" data-toggle="tab"><?php echo $tab_reminder ?></a></li>
            <li><a href="#tab-credit-card" data-toggle="tab"><?php echo $tab_credit_card ?></a></li>
            <li><a href="#tab-billet" data-toggle="tab"><?php echo $tab_billet ?></a></li>
            <li><a href="#tab-order-status" data-toggle="tab"><?php echo $tab_order_status ?></a></li>
            <!--<li><a href="#tab-donation" data-toggle="tab"><?php echo $tab_donation ?></a></li>-->
            <li><a href="#tab-support" data-toggle="tab"><?php echo $tab_support ?></a></li>
          </ul>
          
          <div class="tab-content">
            <!-- Tab Config -->
            <div id="tab-config" class="tab-pane active">
              <!-- Input Status -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_status ?></label>
                <div class="col-sm-10">
                  <select name="iugu_status" class="form-control">
                    <?php if ($iugu_status): ?>
                    <option value="1" selected><?php echo $text_enabled ?></option>
                    <?php else: ?>
                    <option value="1"><?php echo $text_enabled ?></option>
                    <?php endif ?>
                    
                    <?php if (!$iugu_status): ?>
                    <option value="0" selected><?php echo $text_disabled ?></option>
                    <?php else: ?>
                    <option value="0"><?php echo $text_disabled ?></option>
                    <?php endif ?>
                  </select>
                  <input type="hidden" name="iugu_discount_status" value="1" />
                  <input type="hidden" name="iugu_interest_status" value="1" />
                </div>
              </div>
              
              <!-- Input Account ID -->
              <div class="form-group required">
                <label class="col-sm-2 control-label"><?php echo $entry_account_id ?></label>
                <div class="col-sm-10">
                  <input type="text" name="iugu_account_id" value="<?php echo $iugu_account_id ?>" placeholder="" class="form-control" />
                </div>
              </div>
              
              <!-- Input Token -->
              <div class="form-group required">
                <label class="col-sm-2 control-label"><?php echo $entry_token ?></label>
                <div class="col-sm-10">
                  <input type="text" name="iugu_token" value="<?php echo $iugu_token ?>" placeholder="" class="form-control" />
                </div>
              </div>
              
              <!-- Input Status -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_test_mode ?></label>
                <div class="col-sm-10">
                  <select name="iugu_test_mode" class="form-control">
                    <?php if ($iugu_test_mode): ?>
                    <option value="1" selected><?php echo $text_enabled ?></option>
                    <?php else: ?>
                    <option value="1"><?php echo $text_enabled ?></option>
                    <?php endif ?>
                    
                    <?php if (!$iugu_test_mode): ?>
                    <option value="0" selected><?php echo $text_disabled ?></option>
                    <?php else: ?>
                    <option value="0"><?php echo $text_disabled ?></option>
                    <?php endif ?>
                  </select>
                </div>
              </div>
              
              <!-- Input custom Field (Número) -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_custom_field_number ?></label>
                <div class="col-sm-10">
                  <span class="input-group">
                    <select name="iugu_custom_field_number" class="form-control">
                      <?php foreach($custom_fields as $custom_field): ?>
                      <?php if ($custom_field['custom_field_id'] == $iugu_custom_field_number): ?>
                      <option value="<?php echo $custom_field['custom_field_id'] ?>" selected><?php echo $custom_field['name'] ?></option>
                      <?php else: ?>
                      <option value="<?php echo $custom_field['custom_field_id'] ?>"><?php echo $custom_field['name'] ?></option>
                      <?php endif ?>
                      <?php endforeach ?>
                    </select>
                    <span class="input-group-btn">
                      <a href="<?php echo $add_custom_field ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                    </span>
                  </span>
                </div>
              </div>
              
              <!-- Input custom Field (CPF) -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_custom_field_cpf ?></label>
                <div class="col-sm-10">
                  <span class="input-group">
                    <select name="iugu_custom_field_cpf" class="form-control">
                      <?php foreach($custom_fields as $custom_field): ?>
                      <?php if ($custom_field['custom_field_id'] == $iugu_custom_field_cpf): ?>
                      <option value="<?php echo $custom_field['custom_field_id'] ?>" selected><?php echo $custom_field['name'] ?></option>
                      <?php else: ?>
                      <option value="<?php echo $custom_field['custom_field_id'] ?>"><?php echo $custom_field['name'] ?></option>
                      <?php endif ?>
                      <?php endforeach ?>
                    </select>
                    <span class="input-group-btn">
                      <a href="<?php echo $add_custom_field ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                    </span>
                  </span>
                </div>
              </div>
              
              <!-- Input Geo Zone -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_geo_zone ?></label>
                <div class="col-sm-10">
                  <select name="iugu_geo_zone_id" class="form-control">
                    <option value=""><?php echo $text_all_zones ?></option>
                    <?php foreach($geo_zones as $geo_zone): ?>
                    <?php if ($geo_zone['geo_zone_id'] == $iugu_geo_zone_id): ?>
                    <option value="<?php echo $geo_zone['geo_zone_id'] ?>" selected><?php echo $geo_zone['name'] ?></option>
                    <?php else: ?>
                    <option value="<?php echo $geo_zone['geo_zone_id'] ?>"><?php echo $geo_zone['name'] ?></option>
                    <?php endif ?>
                    <?php endforeach ?>
                  </select>
                </div>
              </div>
              
              <!-- Input Sort Order -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_sort_order ?></label>
                <div class="col-sm-10">
                  <input type="number" name="iugu_sort_order" value="<?php echo $iugu_sort_order ?>" class="form-control" />
                  <input type="hidden" name="iugu_discount_sort_order" value="3" />
                  <input type="hidden" name="iugu_interest_sort_order" value="3" />
                </div>
              </div>
            </div>
            
            <!-- Tab Reminder -->
            <div id="tab-reminder" class="tab-pane">
            
              <!-- Input Reminder Status -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_reminder_status ?></label>
                <div class="col-sm-10">
                  <select name="iugu_reminder_invoice_status" class="form-control">
                    <?php if ($iugu_reminder_invoice_status): ?>
                    <option value="1" selected><?php echo $text_enabled ?></option>
                    <?php else: ?>
                    <option value="1"><?php echo $text_enabled ?></option>
                    <?php endif ?>
                    
                    <?php if (!$iugu_reminder_invoice_status): ?>
                    <option value="0" selected><?php echo $text_disabled ?></option>
                    <?php else: ?>
                    <option value="0"><?php echo $text_disabled ?></option>
                    <?php endif ?>
                  </select>
                </div>
              </div>
              
              <!-- Input Reminder Discount -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_discount ?></label>
                <div class="col-sm-10">
                  <div class="col-sm-6">
                    <input type="text" name="iugu_reminder_discount[value]" value="<?php echo $iugu_reminder_discount['value'] ?>" placeholder="" class="form-control" />
                  </div>
                  <div class="col-sm-6">
                    <select name="iugu_reminder_discount[type]" class="form-control">
                      <option value="P" <?php echo ($iugu_reminder_discount['type'] == 'P') ? 'selected' : '' ?>><?php echo $text_percentual ?></option>
                      <option value="F" <?php echo ($iugu_reminder_discount['type'] == 'F') ? 'selected' : '' ?>><?php echo $text_fixed ?></option>
                    </select>
                  </div>
                </div>
              </div>
              
              <!-- Input Reminder Interest -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_interest ?></label>
                <div class="col-sm-10">
                  <div class="col-sm-6">
                    <input type="text" name="iugu_reminder_interest[value]" value="" placeholder="" class="form-control" />
                  </div>
                  <div class="col-sm-6">
                    <select name="iugu_reminder_interest[type]" class="form-control">
                      <option value="P" <?php echo ($iugu_reminder_interest['type'] == 'P') ? 'selected' : '' ?>><?php echo $text_percentual ?></option>
                      <option value="F" <?php echo ($iugu_reminder_interest['type'] == 'F') ? 'selected' : '' ?>><?php echo $text_fixed ?></option>
                    </select>
                  </div>
                </div>
              </div>
              
              <!-- Input Expiration -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_reminder_expiration ?></label>
                <div class="col-sm-10">
                  <span class="input-group">
                    <input type="number" name="iugu_reminder_expiration" value="<?php echo $iugu_reminder_expiration ?>" placeholder="" class="form-control" />
                    <span class="input-group-addon"><?php echo $text_days ?></span>
                  </span>
                </div>
              </div>
              
              <!-- Input Reminder Payment Method -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_payment_method ?></label>
                <div class="col-sm-10">
                  <select name="iugu_reminder_payment_method" class="form-control">
                    <option value="all" <?php echo ($iugu_reminder_payment_method == 'all') ? 'selected' : '' ?>><?php echo $text_all ?></option>
                    <option value="credit_card" <?php echo ($iugu_reminder_payment_method == 'credit_card') ? 'selected' : '' ?>><?php echo $text_credit_card ?></option>
                    <option value="bank_slip" <?php echo ($iugu_reminder_payment_method == 'bank_slip') ? 'selected' : '' ?>><?php echo $text_billet ?></option>
                  </select>
                </div>
              </div>
            </div>
            
            <!-- Tab Credit Card -->
            <div id="tab-credit-card" class="tab-pane">
            
              <!-- Input Credit Card Status -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_status ?></label>
                <div class="col-sm-10">
                  <select name="iugu_credit_card_status" class="form-control">
                    <option value="1" <?php echo ($iugu_credit_card_status) ? 'selected' : '' ?>><?php echo $text_enabled ?></option>
                    <option value="0" <?php echo (!$iugu_credit_card_status) ? 'selected' : '' ?>><?php echo $text_disabled ?></option>
                  </select>
                </div>
              </div>
            
              <!-- Input Credit Card Discount -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_discount ?></label>
                <div class="col-sm-10">
                  <div class="col-sm-6">
                    <input type="text" name="iugu_credit_card_discount[value]" value="<?php echo $iugu_credit_card_discount['value'] ?>" placeholder="" class="form-control" />
                  </div>
                  <div class="col-sm-6">
                    <select name="iugu_credit_card_discount[type]" class="form-control">
                      <option value="P" <?php echo ($iugu_credit_card_discount['type'] == 'P') ? 'selected' : '' ?>><?php echo $text_percentual ?></option>
                      <option value="F" <?php echo ($iugu_credit_card_discount['type'] == 'F') ? 'selected' : '' ?>><?php echo $text_fixed ?></option>
                    </select>
                  </div>
                </div>
              </div>
            
              <!-- Input Credit Card Discount -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_interest ?></label>
                <div class="col-sm-10">
                  <div class="col-sm-6">
                    <input type="text" name="iugu_credit_card_interest[value]" value="<?php echo $iugu_credit_card_interest['value'] ?>" placeholder="" class="form-control" />
                  </div>
                  <div class="col-sm-6">
                    <select name="iugu_credit_card_interest[type]" class="form-control">
                      <option value="P" <?php echo ($iugu_credit_card_interest['type'] == 'P') ? 'selected' : '' ?>><?php echo $text_percentual ?></option>
                      <option value="F" <?php echo ($iugu_credit_card_interest['type'] == 'F') ? 'selected' : '' ?>><?php echo $text_fixed ?></option>
                    </select>
                  </div>
                </div>
              </div>
            
              <!-- Input Credit Card Installments Status -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_installments ?></label>
                <div class="col-sm-10">
                  <select name="iugu_credit_card_installments_status" class="form-control">
                    <option value="1" <?php echo ($iugu_credit_card_installments_status) ? 'selected' : '' ?>><?php echo $text_enabled ?></option>
                    <option value="0" <?php echo (!$iugu_credit_card_installments_status) ? 'selected' : '' ?>><?php echo $text_disabled ?></option>
                  </select>
                </div>
              </div>
            
              <!-- Input Credit Card Quantity Installments -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_qnt_max_installments ?></label>
                <div class="col-sm-10">
                  <select name="iugu_qnt_max_installments" class="form-control">
                    <?php for($i = 1; $i <= 12; $i++): ?>
                    <option value="<?php echo $i ?>" <?php echo ($iugu_qnt_max_installments == $i) ? 'selected' : '' ?>><?php echo $i ?></option>
                    <?php endfor ?>
                  </select>
                </div>
              </div>
            
              <!-- Input Credit Card Installments Interest-Free -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_qnt_installments_interest_free ?></label>
                <div class="col-sm-10">
                  <select name="iugu_qnt_installments_interest_free" class="form-control">
                    <?php for($i = 0; $i <= 12; $i++): ?>
                    <option value="<?php echo $i ?>" <?php echo ($iugu_qnt_installments_interest_free == $i) ? 'selected' : '' ?>><?php echo $i ?></option>
                    <?php endfor ?>
                  </select>
                </div>
              </div>
            
              <!-- Input Credit Card Tax -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_credit_card_tax ?></label>
                <div class="col-sm-10">
                  <span class="input-group">
                    <input text="number" name="iugu_credit_card_tax" value="<?php echo $iugu_credit_card_tax ?>" placeholder="" class="form-control" />
                    <span class="input-group-addon">%</span>
                  </span>
                  <?php if ($error_iugu_tax): ?>
                  <span class="text-danger"><?php echo $error_iugu_tax ?></span>
                  <?php endif ?>
                </div>
              </div>
            </div>
            
            <!-- Tab Billet -->
            <div id="tab-billet" class="tab-pane">
            
              <!-- Input Billet Status -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_status ?></label>
                <div class="col-sm-10">
                  <select name="iugu_billet_status" class="form-control">
                    <option value="1" <?php echo ($iugu_billet_status) ? 'selected' : '' ?>><?php echo $text_enabled ?></option>
                    <option value="0" <?php echo (!$iugu_billet_status) ? 'selected' : '' ?>><?php echo $text_disabled ?></option>
                  </select>
                </div>
              </div>
            
              <!-- Input Billet Discount -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_discount ?></label>
                <div class="col-sm-10">
                  <div class="col-sm-6">
                    <input type="text" name="iugu_billet_discount[value]" value="<?php echo $iugu_billet_discount['value'] ?>" placeholder="" class="form-control" />
                  </div>
                  
                  <div class="col-sm-6">
                    <select name="iugu_billet_discount[type]" class="form-control">
                      <option value="P" <?php echo ($iugu_billet_discount['type'] == 'P') ? 'selected' : '' ?>><?php echo $text_percentual ?></option>
                      <option value="F" <?php echo ($iugu_billet_discount['type'] == 'F') ? 'selected' : '' ?>><?php echo $text_fixed ?></option>
                    </select>
                  </div>
                </div>
              </div>
            
              <!-- Input Billet Discount -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_interest ?></label>
                <div class="col-sm-10">
                  <div class="col-sm-6">
                    <input type="text" name="iugu_billet_interest[value]" value="<?php echo $iugu_billet_interest['value'] ?>" placeholder="" class="form-control" />
                  </div>
                  
                  <div class="col-sm-6">
                    <select name="iugu_billet_interest[type]" class="form-control">
                      <option value="P" <?php echo ($iugu_billet_interest['type'] == 'P') ? 'selected' : '' ?>><?php echo $text_percentual ?></option>
                      <option value="F" <?php echo ($iugu_billet_interest['type'] == 'F') ? 'selected' : '' ?>><?php echo $text_fixed ?></option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          
            <!-- Tab Order Status -->
            <div id="tab-order-status" class="tab-pane">
              
              <!-- Input Order Status (Draft) -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_draft ?></label>
                <div class="col-sm-10">
                  <select name="iugu_order_status_draft" class="form-control">
                    <?php foreach($order_statuses as $order_status): ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>" <?php echo ($order_status['order_status_id'] == $iugu_order_status_draft) ? 'selected' : '' ?>><?php echo $order_status['name'] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>
              </div>
              
              <!-- Input Order Status (Pending) -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_pending ?></label>
                <div class="col-sm-10">
                  <select name="iugu_order_status_pending" class="form-control">
                    <?php foreach($order_statuses as $order_status): ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>" <?php echo ($order_status['order_status_id'] == $iugu_order_status_pending) ? 'selected' : '' ?>><?php echo $order_status['name'] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>
              </div>
              
              <!-- Input Order Status (Partially Paid) -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_partially_paid ?></label>
                <div class="col-sm-10">
                  <select name="iugu_order_status_partially_paid" class="form-control">
                    <?php foreach($order_statuses as $order_status): ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>" <?php echo ($order_status['order_status_id'] == $iugu_order_status_partially_paid) ? 'selected' : '' ?>><?php echo $order_status['name'] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>
              </div>
              
              <!-- Input Order Status (Paid) -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_paid ?></label>
                <div class="col-sm-10">
                  <select name="iugu_order_status_paid" class="form-control">
                    <?php foreach($order_statuses as $order_status): ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>" <?php echo ($order_status['order_status_id'] == $iugu_order_status_paid) ? 'selected' : '' ?>><?php echo $order_status['name'] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>
              </div>
              
              <!-- Input Order Status (Canceled) -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_canceled ?></label>
                <div class="col-sm-10">
                  <select name="iugu_order_status_canceled" class="form-control">
                    <?php foreach($order_statuses as $order_status): ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>" <?php echo ($order_status['order_status_id'] == $iugu_order_status_canceled) ? 'selected' : '' ?>><?php echo $order_status['name'] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>
              </div>
              
              <!-- Input Order Status (Refunded) -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_refunded ?></label>
                <div class="col-sm-10">
                  <select name="iugu_order_status_refunded" class="form-control">
                    <?php foreach($order_statuses as $order_status): ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>" <?php echo ($order_status['order_status_id'] == $iugu_order_status_refunded) ? 'selected' : '' ?>><?php echo $order_status['name'] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>
              </div>
              
              <!-- Input Order Status (Expired) -->
              <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_expired ?></label>
                <div class="col-sm-10">
                  <select name="iugu_order_status_expired" class="form-control">
                    <?php foreach($order_statuses as $order_status): ?>
                    <option value="<?php echo $order_status['order_status_id'] ?>" <?php echo ($order_status['order_status_id'] == $iugu_order_status_expired) ? 'selected' : '' ?>><?php echo $order_status['name'] ?></option>
                    <?php endforeach ?>
                  </select>
                </div>
              </div>
            </div>
            
            <!-- Tab Donation -->
            <!--<div id="tab-donation" class="tab-pane active"></div>-->
          
            <!-- Tab Support -->
            <div id="tab-support" class="tab-pane">
              <article class="col-md-12 live-chat">
                <h2 class="title clearfix">
                  <span class="fa fa-question-circle"></span>
                  <span class="text"><?php echo $text_support_heading ?></span>
                </h2>
                <p><?php echo $text_support_description ?></p>
                <a href="http://www.valdeirsantana.com.br/<?php echo $text_support_url ?>" class="btn browse"><?php echo $text_support_button ?></a>
                <a href="http://www.valdeirsantana.com.br/<?php echo $text_support_url ?>" class="overlay-link" title="Valdeir Santana"></a>
              </article>
            </div>
          </div>
        </form> <!-- /Form -->
      </div> <!-- /.Panel-Body -->
    </div> <!-- /.Panel -->
  </div> <!-- /.container-fluid -->
</div> <!-- /#Content -->
<?php echo $footer ?>