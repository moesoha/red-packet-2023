setTimeout(function () {
	var fun = function () {
		var $ = this;
		switch($.type) {
			case 'radio':
				if(!$.checked) return;
				switch($.id) {
					case 'radio-role-staff':
						document.getElementById('label-xuegonghao').textContent = '工号';
						return;
					case 'radio-role-student':
						document.getElementById('label-xuegonghao').textContent = '学号';
						return;
					case 'radio-role-guest':
						document.getElementById('label-xuegonghao').hidden = true;
						document.getElementById('xuegonghao').hidden = true;
						return;
				}
				return;
		}
	}
	var $$ = document.getElementsByTagName('input');
	var i = 0;
	for(; i < $$.length; i++) {
		var $ = $$[i];
		(function () { var $_ = $; setTimeout(function () { fun.apply($_); }, 0); })();
		$.addEventListener('change', fun);
	}
}, 0);

setTimeout(function () {
	var fun = function (event) {
		var $ = this;
		switch($.id) {
			case 'form-zizhureg':
				if(!$.elements['username'].value) {
					alert('用户名不能为空！');
				} else if(!$.elements['password'].value) {
					alert('密码不能为空！');
				} else if($.elements['password'].value !== $.elements['password-again'].value) {
					alert('两次密码输入不一致！');
				} else {
					return;
				}
				event.preventDefault();
				return;
		}
	};
	var $$ = document.getElementsByTagName('form');
	var i = 0;
	for(; i < $$.length; i++) {
		var $ = $$[i];
		$.addEventListener('submit', fun);
	}
}, 0);
