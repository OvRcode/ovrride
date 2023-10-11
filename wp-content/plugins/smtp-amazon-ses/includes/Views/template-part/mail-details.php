<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="yay-sidenav yay-smtp-mail-detail-drawer">
  <a href="javascript:void(0)" class="closebtn">&times;</a>
  <div class="yay-smtp-layout-activity-panel-content">
	<div class="yay-smtp-activity-panel-header">
	  <h3 class="yay-smtp-activity-panel-header-title">Email log #1</h3>
	</div>
	<div class="yay-smtp-activity-panel-content panel-content">
	  <table>
		<tr>
		  <td>
			<div class="content-el datetime-el">
			  <span class="title">Time:</span>
			  <span class="content"></span>
			</div>
		  </td>
		  <td>
		  <div class="content-el from-el">
			<span class="title">From:</span>
			<span class="content"></span>
		  </div>
		  </td>
		</tr>
		<tr>
		  <td>
		  <div class="content-el to-el">
			<span class="title">To: </span>
			<span class="content"></span>
		  </div>
		  </td>
		  <td>
		  <div class="content-el subject-el">
			<span class="title">Subject: </span>
			<span class="content"></span>
		  </div>
		  </td>
		</tr>
		<tr>
		  <td>
		  <div class="content-el mailer-el">
			<span class="title">Mailer: </span>
			<span class="content"></span>
		  </div>
		  </td>
		  <td>
			<div class="content-el status-el">
			  <span class="title">Status: </span>
			  <mark><span class="content"></span></mark>
			  <span class="reason_error"></span>
			</div>
		  </td>
		</tr>
	  </table>
	  <div class="mail-body-el" style="padding: 10px 15px; border-bottom: 1px solid #ddd; vertical-align: baseline;">
		<div class="content-el">
		  <span class="title">Email Body</span>
		</div>
	  </div>
	  <div class="mail-body-el mail-body-content-detail" style="padding: 10px 15px; border-bottom: 1px solid #ddd; vertical-align: baseline;">

	  </div>
	</div>
  </div>
</div>
