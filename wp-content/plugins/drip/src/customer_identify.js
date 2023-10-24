(function(dwid) {
  const _q = (typeof _dcq !== 'undefined' && _dcq !== null) ? _dcq : [];
  const _d = (typeof dwid !== 'undefined' && dwid !== null) ? dwid : {found: false};
  if(_d.found) { _q.push(["identify", { email: _d.id, drip_unknown_status: true }]); }
})(drip_woocommerce_identify_data);
