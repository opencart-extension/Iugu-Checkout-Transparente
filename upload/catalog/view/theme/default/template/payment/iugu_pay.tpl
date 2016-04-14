<!--
	Author: Valdeir Santana
	Site: http://www.valdeirsantana.com.br
	License: http://www.gnu.org/licenses/gpl-3.0.en.html
-->
<?php echo $header ?>
<style>
  iframe {width:100%;height:1500px !important;border:none;display:block}
</style>

<div class="container-fluid">
  <iframe src="<?php echo $invoice ?>"></iframe>
</div>

<?php echo $footer ?>