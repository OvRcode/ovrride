function showProgress() {
	window.scrollTo(0,0);
	document.getElementById('progress').style.display = 'block';
	document.getElementById('content').style.display = 'none';
	document.getElementById('support-donate_rate').style.display = 'none';
}

var $j = jQuery.noConflict();
$j(function() {

	$j('#postform').submit(function() {
		if( $j('#delete_temporary_csv').val() == 1 ) {
			showProgress();
			return false;
		}
	});

	// Date Picker
	$j('.datepicker').datepicker({
		dateFormat: 'dd/mm/yy'
	});

	// Chosen
	$j(".chzn-select").chosen({
		search_contains: true
	});

	$j('#export-products').show();
	// Categories
	$j('#export-products-filters-categories').hide();
	if( $j('#products-filters-categories').attr('checked') ) {
		$j('#export-products-filters-categories').show();
	}
	// Tags
	$j('#export-products-filters-tags').hide();
	if( $j('#products-filters-tags').attr('checked') ) {
		$j('#export-products-filters-tags').show();
	}
	// Product Status
	$j('#export-products-filters-status').hide();
	if( $j('#products-filters-status').attr('checked') ) {
		$j('#export-products-filters-status').show();
	}
	$j('#export-products-filters-type').hide();
	if( $j('#products-filters-type').attr('checked') ) {
		$j('#export-products-filters-type').show();
	}
	$j('#export-categories').hide();
	$j('#export-tags').hide();
	$j('#export-orders').hide();
	$j('#export-orders-filters-status').hide();
	if( $j('#orders-filters-status').attr('checked') ) {
		$j('#export-orders-filters-status').show();
	}
	$j('#export-orders-filters-date').hide();
	if( $j('#orders-filters-date').attr('checked') ) {
		$j('#export-orders-filters-date').show();
	}
	$j('#export-customers').hide();
	$j('#export-coupons').hide();

	$j('#products-filters-categories').click(function(){
		$j('#export-products-filters-categories').toggle();
	});
	$j('#products-filters-tags').click(function(){
		$j('#export-products-filters-tags').toggle();
	});
	$j('#products-filters-status').click(function(){
		$j('#export-products-filters-status').toggle();
	});
	$j('#products-filters-type').click(function(){
		$j('#export-products-filters-type').toggle();
	});
	$j('#orders-filters-status').click(function(){
		$j('#export-orders-filters-status').toggle();
	});
	$j('#orders-filters-date').click(function(){
		$j('#export-orders-filters-date').toggle();
	});

	// Export types
	$j('#products').click(function(){
		$j('#export-products').show();
		$j('#export-categories').hide();
		$j('#export-tags').hide();
		$j('#export-orders').hide();
		$j('#export-customers').hide();
		$j('#export-coupons').hide();
	});
	$j('#categories').click(function(){
		$j('#export-products').hide();
		$j('#export-categories').show();
		$j('#export-tags').hide();
		$j('#export-orders').hide();
		$j('#export-customers').hide();
		$j('#export-coupons').hide();
	});
	$j('#tags').click(function(){
		$j('#export-products').hide();
		$j('#export-categories').hide();
		$j('#export-tags').show();
		$j('#export-orders').hide();
		$j('#export-customers').hide();
		$j('#export-coupons').hide();
	});
	$j('#orders').click(function(){
		$j('#export-products').hide();
		$j('#export-categories').hide();
		$j('#export-tags').hide();
		$j('#export-orders').show();
		$j('#export-customers').hide();
		$j('#export-coupons').hide();
	});
	$j('#customers').click(function(){
		$j('#export-products').hide();
		$j('#export-categories').hide();
		$j('#export-tags').hide();
		$j('#export-orders').hide();
		$j('#export-customers').show();
		$j('#export-coupons').hide();
	});
	$j('#coupons').click(function(){
		$j('#export-products').hide();
		$j('#export-categories').hide();
		$j('#export-tags').hide();
		$j('#export-orders').hide();
		$j('#export-customers').hide();
		$j('#export-coupons').show();
	});

	// Export button
	$j('#export_products').click(function(){
		$j('input:radio[name=dataset]:nth(0)').attr('checked',true);
	});
	$j('#export_orders').click(function(){
		$j('input:radio[name=dataset]:nth(3)').attr('checked',true);
	});
	$j('#export_customers').click(function(){
		$j('input:radio[name=dataset]:nth(4)').attr('checked',true);
	});
	$j('#export_coupons').click(function(){
		$j('input:radio[name=dataset]:nth(5)').attr('checked',true);
	});

	// Select all
	$j('.checkall').click(function () {
		$j(this).closest('.postbox').find(':checkbox').attr('checked', true);
	});
	$j('.uncheckall').click(function () {
		$j(this).closest('.postbox').find(':checkbox').attr('checked', false);
	});

});