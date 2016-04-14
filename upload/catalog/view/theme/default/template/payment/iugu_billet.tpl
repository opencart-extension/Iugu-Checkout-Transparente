<!--
	Author: Valdeir Santana
	Site: http://www.valdeirsantana.com.br
	License: http://www.gnu.org/licenses/gpl-3.0.en.html
-->
<?php if ($errors !== false): ?>
  <?php foreach($errors as $error): ?>
    <div class="alert alert-danger">
      <?php echo $error ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
  <?php endforeach ?>
<?php else: ?>
  <link href="catalog/view/javascript/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="print" />
  <link href="catalog/view/theme/default/stylesheet/plugin/iugu/billet.css" media="all" rel="stylesheet" type="text/css">

  <div class="print">
    <div class="invoice">
      <?php echo $billet ?>
    </div>
  </div><!-- /.modal -->

  <div class="modal fade" id="modal-billet">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><?php echo $heading_billet ?></h4>
        </div>
        <div class="modal-body"></div>
        <div class="modal-footer">
          <button type="button" data-loading-text="<?php echo $text_loading ?>" class="btn btn-default" data-dismiss="modal"><i class="fa fa-reply"></i> <?php echo $button_Cancel ?></button>
          <button type="button" id="button-download" class="btn btn-success"><i class="fa fa-download"></i> <?php echo $button_download ?></button>
          <button type="button" id="button-send-mail" data-loading-text="<?php echo $text_loading ?>" class="btn btn-info"><i class="fa fa-envelope-o"></i> <?php echo $button_send_mail ?></button>
          <button type="button" onClick="javascript:window.print()" data-loading-text="<?php echo $text_loading ?>" class="btn btn-primary"><i class="fa fa-print"></i> <?php echo $button_print ?></button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

  <div class="text-center">
    <button type="button" data-toggle="modal" data-target="#modal-billet" class="btn btn-primary"><i class="fa fa-print"></i> <?php echo $button_print ?></button>
  </div>

  <script>
    $('.invoice a.print-button').remove();
    $('.invoice a.pay-bank-slip-button').remove();
    
    var screen = $('.print').html();
    $(screen).find('.printer').remove();
    
    var printer = $('.print').html();
    $(printer).find('div.screen.hidden-print').remove();
    
    $('.print').remove();
    
    $('body').append('<div id="print">' + $(printer).html() + '</div>');
    $('#modal-billet .modal-body').append(screen);
    $('#print *').addClass('visible-print');
    
    $('#button-send-mail').click(function(){
      $.ajax({
        url: '<?php echo $billet_send_mail ?>',
        beforeSend: function(){
          $('#button-send-mail').button('loading');
        },
        complete: function(){
          window.location.href = '<?php echo $continue ?>';
        }
      });
    });
    
    $('#button-download').click(function(){
      window.location.href = '<?php echo $billet_download ?>';
    });
    
    $('#modal-billet').on('hidden.bs.modal', function (e) {
      window.location.href = '<?php echo $continue ?>';
    })
    
    function printBillet() {
      window.print();
      window.location.href = '<?php echo $continue ?>';
    }
  </script>
<?php endif ?>