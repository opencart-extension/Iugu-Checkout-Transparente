<!--
	Author: Valdeir Santana
	Site: http://www.valdeirsantana.com.br
	License: http://www.gnu.org/licenses/gpl-3.0.en.html
-->
<style>
/* entire container, keeps perspective */
.flip-container {
	perspective: 1000;
	transform-style: preserve-3d;
  height:180px
}
	/*  UPDATED! flip the pane when hovered */
	.flip-container-hover .back {
		transform: rotateY(0deg);
	}
	.flip-container-hover .front {
	    transform: rotateY(180deg);
	}

.flip-container, .front, .back {
	height: 180px;
}

/* flip speed goes here */
.flipper {
	transition: 0.6s;
	transform-style: preserve-3d;

	position: relative;
}

/* hide back of pane during swap */
.front, .back {
	backface-visibility: hidden;
	transition: 0.6s;
	transform-style: preserve-3d;

	position: absolute;
	top: 0;
	left: 0;
}

/*  UPDATED! front pane, placed above back */
.front {
	z-index: 2;
	transform: rotateY(0deg);
}

/* back, initially hidden pane */
.back {
	transform: rotateY(-180deg);
}

.logo-iugu {
  position: absolute;
  bottom: -200px;
  right: 27px;
}
</style>

<div class="container-fluid" id="wrapper-iugu">
  <div class="col-sm-6">
    <div class="form-horizontal" id="form-credit-card">
      
      <!-- Número do Cartão -->
      <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo $text_credit_card_number ?></label>
        <div class="col-sm-10">
          <input type="number" name="number" placeholder="<?php echo $placeholder_number ?>" class="form-control" />
        </div>
      </div>
      
      <!-- Nome do Cliente -->
      <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo $text_credit_card_customer ?></label>
        <div class="col-sm-10">
          <input type="text" name="full_name" placeholder="<?php echo $placeholder_full_name ?>" class="form-control" />
        </div>
      </div>
      
      <!-- Validate do Cartão -->
      <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo $text_credit_card_validate ?></label>
        <div class="col-sm-10">
          <input type="text" name="credit_card_expiration" placeholder="<?php echo $placeholder_credit_card_expiration ?>" class="form-control" />
        </div>
      </div>
      
      <!-- Código de verificação -->
      <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo $text_credit_card_cvv ?></label>
        <div class="col-sm-10">
          <input type="number" name="verification_value" placeholder="<?php echo $placeholder_verification_value ?>" class="form-control" />
        </div>
      </div>
      
      <!-- Parcelamento -->
      <?php if ($installments !== false): ?>
      <div class="form-group">
        <label class="col-sm-2 control-label"><?php echo $text_installments ?></label>
        <div class="col-sm-10">
          <select name="installments" class="form-control">
            <?php foreach($installments as $installment => $value): ?>
            <option value="<?php echo $installment ?>"><?php echo sprintf($text_installment, $installment, $value['text_value']) ?></option>
            <?php endforeach ?>
          </select>
        </div>
      </div>
      <?php endif ?>
      
      <!-- Botão de pagamento -->
      <div class="form-group">
        <label class="col-sm-2 control-label">
          <input type="hidden" name="credit_card_brand" />
        </label>
        <div class="col-sm-10">
          <button type="button" class="btn btn-primary" data-loading-text="<?php echo $text_loading ?>"><?php echo $button_pay ?></button>
        </div>
      </div>
    </div>
  </div>

  <div class="flip-container col-sm-6">
    <div class="flipper">
      <div class="front">
        <div id="credit-card-example-number" style="border: 3px solid #F00;position: absolute;height: 27px;width: 207px;top: 90px;left: 20px;opacity: 0;z-index:1"></div>
        <div id="credit-card-example-validate" style="border: 3px solid #F00;position: absolute;height: 32px;width: 67px;top: 111px;left: 122px;opacity: 0"></div>
        <div id="credit-card-example-customer" style="border: 3px solid #F00;position: absolute;height: 27px;width: 130px;top: 141px;left: 20px;opacity: 0"></div>
        <div id="credit-card-example-logo" style="background: #FFF url(catalog/view/theme/default/image/iugu_credit_card_brands.png) center 0 no-repeat;position: absolute;height: 43px;width: 63px;top: 114px;left: 208px;border-radius: 8px;opacity: 0"></div>
        <img src="catalog/view/theme/default/image/CreditCardFront.gif" style="height:180px" />
      </div>
      <div class="back">
        <div id="credit-card-example-ccv" style="border: 3px solid #F00;position: absolute;height: 37px;width: 50px;top: 60px;left: 225px;opacity: 0"></div>
        <img src="catalog/view/theme/default/image/CreditCardBack.gif" style="height:180px" />
      </div>
      <div class="logo-iugu">
        <a href="http://iugu.com/" target="_blank"><img src="catalog/view/theme/default/image/logo-iugu.png" /></a>
      </div>
    </div>
  </div>
</div>

<script>
  $('input[name="verification_value"]').focus(function(){
    $('.flip-container').toggleClass('flip-container-hover');
  });
  $('input[name="verification_value"]').blur(function(){
    $('.flip-container').toggleClass('flip-container-hover');
  });
  
  $('input[name="number"]').focus(function(){
    $('#credit-card-example-number').stop().animate({
      opacity:1
    }, 1000);
  });
  
  $('input[name="full_name"]').focus(function(){
    $('#credit-card-example-customer').stop().animate({
      opacity:1
    }, 1000);
  });
  
  $('input[name="credit_card_expiration"]').focus(function(){
    $('#credit-card-example-validate').stop().animate({
      opacity:1
    }, 1000);
  });
  
  $('input[name="verification_value"]').focus(function(){
    $('#credit-card-example-ccv').stop().animate({
      opacity:1
    }, 1500);
  });
  
  $('input').blur(function(){
    $('.flip-container .front div:not(#credit-card-example-logo), .flip-container .back div').stop().animate({
      opacity:0
    }, 1000);
  });
  
  var brands_position = {
    mastercard: '3px',
    visa: '-52px',
    amex: '-108px',
    diners: '-165px'
  }
  
  $('input[name="number"]').keyup(function(){
    if ($(this).val().length <= 6) {
      
      var brand = Iugu.utils.getBrandByCreditCardNumber($(this).val())
      $('input[name="credit_card_brand"]').val(brand);
      
      $('#credit-card-example-logo').stop().animate({
        'background-position-x': 0,
        'background-position-y': brands_position[brand],
        opacity:1
      }, 1500);
    }
  });
</script>

<script>
Iugu.setAccountID('<?php echo $iugu_account_id ?>');

<?php if ($test_mode): ?>
Iugu.setTestMode(true);
<?php endif ?>

$('#form-credit-card button').click(function(){
  /* Títular do Cartão */
  var fullname = $('input[name="full_name"]').val().split(' ');
  var firstname = fullname[0];
  fullname.shift();
  var lastname = fullname.join(' ');
  
  /* Número do Cartão */
  var credit_card_number = $('input[name="number"]').val();
  
  /* Bandeira do Cartão */
  var credit_card_brand = $('input[name="credit_card_brand"]').val();
  
  /* Validade do Cartão */
  var expiration_month = $('input[name="credit_card_expiration"]').val().split('/').shift();
  var expiration_year = $('input[name="credit_card_expiration"]').val().split('/').reverse().shift();
  
  /* Código de Segurançã */
  var cvv = $('input[name="verification_value"]').val();
  
  var error = false;
  
  $('.alert, .text-danger').remove();
  
  /* Verifica se o número do cartão está correto */
  if (Iugu.utils.validateCreditCardNumber(credit_card_number) == false) {
    $('input[name="number"]').after('<span class="text-danger">Cartão inválido!</span>');
    error = true;
  }
  
  /* Verifica validate do código de segurança */
  if (Iugu.utils.validateCVV(cvv, credit_card_brand) == false) {
    $('input[name="verification_value"]').after('<span class="text-danger">Código inválido!</span>');
    error = true;
  }
  
  /* Verifica a validade da data de validade */
  if (Iugu.utils.validateExpiration(expiration_month, expiration_year) == false) {
    $('input[name="verification_value"]').after('<span class="text-danger">Data inválida!</span>');
    error = true;
  }
  
  /* Captura o token e finaliza o pedido */
  if (error == false) {
    var cc = Iugu.CreditCard(credit_card_number, expiration_month, expiration_year, firstname, lastname, cvv);
    
    Iugu.createPaymentToken(cc, function(response) {
      if (response.errors) {
        $.map(response.errors, function(error){
          alert(error);
        })
      } else {
        $.ajax({
          url: '<?php echo $link_pay ?>',
          type: 'POST',
          data: {
            token: response.id,
            installment: typeof($('select[name="installments"]').val() != 'undefined') ? $('select[name="installments"]').val() : 1
          },
          dataType: 'json',
          beforeSend: function() {
            $('#form-credit-card button').button('loading');
          },
          success: function(result) {
            if (result.success == true) {
              var html = '<div class="alert alert-success text-center" style="display:none">';
              html += '<p style="font-size: 21px;margin: 10px 0;">' + result.message + '</p><br>';
              html += '<a href="<?php echo $link_download_invoice ?>" class="btn btn-primary" target="_blank"><?php echo $button_download_invoice ?></a>';
              html += '<a href="<?php echo $link_send_mail_invoice ?>" class="btn btn-info" target="_blank"><?php echo $button_send_mail_invoice ?></a></div>';
              
              $('#wrapper-iugu').append(html);
              
              $('#form-credit-card, .flipper').parent().slideUp().remove();
              
              $('#wrapper-iugu .alert-success').slideDown();
              
              setTimeout(function(){
                window.location.href = '<?php echo $continue ?>';
              }, 5000);
            } else {
              var html = '<div class="alert alert-danger text-center" style="display:none">';
              
              if (isArray(result.errors)) {
                html += '<p style="font-size: 21px;margin: 10px 0;">' + result.errors + '</p>';
              } else {
                $.map(result.errors, function(error){
                  html += '<p style="font-size: 21px;margin: 10px 0;">' + result.error[0] + '</p>';
                });
              }
              html += '</div>';
              
              $('#wrapper-iugu').append(html);
              $('#wrapper-iugu .alert-danger').slideDown();
            }
          },
          complete: function() {
            $('#form-credit-card button').button('reset');
          }
        })
      }
    });
  }
});

function isArray(what) {
    return Object.prototype.toString.call(what) === '[object Array]';
}
</script>