window.onload = function () {

    var messages = { 
		installing : document.getElementById('BDMsgWorkingRegisterUser').value
	}
    var lblWrongEmail           = document.getElementById('lblWrongEmail');
    var btnRegisterUser         = document.getElementById('btnRegisterUser');
    var btnRegisterUserDisable  = document.getElementById('btnRegisterUserDisable');
    var lblWaiting              = document.getElementById('lblWaiting');


    if (btnRegisterUser != null) {
        if ( btnRegisterUser.addEventListener ) {
			btnRegisterUser.addEventListener('click', RegisterUserProgress, false);
		} else {
			btnRegisterUser.attachEvent('onclick', RegisterUserProgress);
		}
    }
     
    function RegisterUserProgress() {
        btnRegisterUser.style.display = "none";
        btnRegisterUserDisable.style.display = "block";
        if (lblWrongEmail != null) { lblWrongEmail.innerHTML = '' };
        lblWaiting.innerHTML = messages.installing;
    }
}
