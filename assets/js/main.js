var curSearch = false;
//ES 2019, any alternative? no
function wait(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function showAlert(head, msg) {
    $('#modal-public').off('**');
    $('#modal-public .modal-head').html(head);
    $('#modal-public .modal-text').html(msg);
    let btnOk = '<a rel="modal:close"><button class="btn" id="btn-ok">OK</button></a>';
    $('#modal-public .modal-foot').html(btnOk);
    $('#modal-public').modal({
        closeExisting: false,
        escapeClose: true,
        clickClose: false,
        showClose: false,
        blockerClass: 'nope'
    });
    //$('#modal-public').on('modal:open', function(ev, modal) {
        $('#btn-ok').focus();
    //});
}
function showConfirm(head, msg) {
    $('#modal-public').off('**');
    $('#modal-public .modal-head').html(head);
    $('#modal-public .modal-text').html(msg);
    let btnYes = '<button class="btn" id="btn-yes">Yes</button>';
    let btnNo = '<button class="btn" id="btn-no">No</button>';
    $('#modal-public .modal-foot').html(btnNo + ' &nbsp; ' + btnYes);
    $('#modal-public').modal({
        closeExisting: false,
        escapeClose: true,
        clickClose: false,
        showClose: false,
        blockerClass: 'nope'
    });
		$('#btn-yes').focus();
}

function formatDecToCurrency(val) {
		let parser;
		if (val == undefined) val = 0;
		if (typeof(val) != "number")
    	parser = val.toString().replace(/[^,\d]/g, '');
		parser = val;
    return new Intl.NumberFormat('id-IDR', {
        maximumFractionDigits: 2,
        minimumFractionDigits: 2,
    }).format(parser);
}

function formatCurToDec(val) {
	if (val == undefined) val = '0';
	let parser = val.toString().replace(/[^,\d]/g, '');
	parser = parser.replace(',', '.');
	return parser;
}
//function support for currency / number input



function enterOnSearch() {
	hideSearchBox();
	let search = curSearch;//$('#search');
	if (!(
		(search.val() == '')
	)) {
		let data = search.data();
		data.name = search.val();
		addToList(data);
	}
	//search.val('');
}
function showSearchBox() {
	curSearch = $(this);
	$('.box-search').css('display', 'block');
	$('.box-search').css('left', curSearch.offset().left +'px');
	$('.box-search').css('top', (curSearch.offset().top + $(this)[0].clientHeight) +'px');
	$('.box-search').css('max-width', $(this).width() + 10 + 'px');
	//$('#search').addClass('active');
	curSearch.addClass('active');
	$('.box-search > input').focus();
	$('.box-search > input').trigger('keyup', [true]);
}
function hideSearchBox() {
	$('.box-search').css('display', 'none');
	curSearch.removeClass('active');
	//curSearch = false;
}
function boxToSearch(index) {
	optIndex = $('.box-search > select option[data-index="'+ index +'"]');
	if (index == 0) return;
	curSearch.val(optIndex.html());
	/*$('#search').data('id_variant', optIndex.val());
	$('#search').data('id_produk', optIndex.data('id_produk'));
	$('#search').data('barcode', optIndex.data('barcode'));*/
	curSearch.data('id', optIndex.val());
	for (let iFor in optIndex.data()) {
		curSearch.data(iFor, optIndex.data(iFor));
	}
	$('.box-search > input').focus();
}
//function searchbox

$(document).ready(function() {
	$('.box-search').prependTo('body');
	//$('label[for]').on('click', function() {
	$(document).on('click', 'label[for]', function() {
			$('input[name="'+ $(this).attr('for') +'"]').trigger('click');
	});

	$(document).on('focusin', '.search', showSearchBox);
	$(document).on('focusout', '.box-search > input', function() {
		if ($('.box-search > select').is(':focus') ||
				$('.box-search > select option').is(':focus') ||
				$('.box-search').is(':focus-within')
			 ) return;
		hideSearchBox();
	});
	$(document).on('keydown', '.box-search > input', function(ev, isFake) {
		if (isFake == true) return true;
		if (ev.originalEvent.key == 'Enter') {
			ev.preventDefault();
			enterOnSearch();
		}
		if (ev.originalEvent.key == 'Escape') {
			hideSearchBox();
		}
		let val = $('.box-search > select').val();
		val = $('.box-search select option[value="'+ val +'"]').data('index');
		if (ev.originalEvent.key == 'ArrowDown') {
			if (val == undefined) {
				$('.box-search > select').val($('.box-search > select option[data-index="0"]').attr('value'));
			} else {
				val++;
				if (val > $('.box-search > select')[0].length) $('.box-search > select').val($('.box-search > select option[data-index="0"]').attr('value'));
				$('.box-search > select').val($('.box-search > select option[data-index="'+ val +'"]').attr('value'));
				//$('#search').val($('.box-search > select option[data-index="'+ val +'"]').html());
				boxToSearch(val);
			}
		} else if (ev.originalEvent.key == 'ArrowUp') {
			if (val == undefined) {
				$('.box-search > select').val($('.box-search > select option[data-index="'+ ($('.box-search > select')[0].length - 1) +'"]').attr('value'));
				boxToSearch($('.box-search > select')[0].length - 1);
			} else {
				val--;
				if (val < 1) $('.box-search > select').val($('.box-search > select option[data-index="0"]').attr('value'));
				$('.box-search > select').val($('.box-search > select option[data-index="'+ val +'"]').attr('value'));
				boxToSearch(val);
			}
		}
	});
	$(document).on('click', '.box-search select option', function() {
		if (!$(this).attr('disabled')) {
			boxToSearch($(this).data('index'));
		}
	});
	$(document).on('dblclick', '.box-search select option', function() {
		if (!$(this).attr('disabled')) {
			enterOnSearch();
		}
	});
	//End of searchbox

	$(document).on('keyup', 'input[data-type="currency"]', function(ev) {
		let val = $(this).val(), bolInit = false;
		if (val.length <= 3) bolInit = true;
		let parser = val.replace(/[^,\d]/g, '').toString();
		parser = parser.replace(',', '.');
		let lenBefore = val.length;
		let money = new Intl.NumberFormat('id-IDR', {
				maximumFractionDigits: 2,
				minimumFractionDigits: 2,
		}).format(parser);
		let lenAfter = money.length;
		let curPosStart = ev.target.selectionStart;
		let curPosEnd = ev.target.selectionEnd;
		if (money !== NaN) $(this).val(money);
		if (bolInit == true) {
			this.selectionStart = 1;
			this.selectionEnd = 1;
		} else {
			this.selectionStart = curPosStart + (lenAfter - lenBefore);
			this.selectionEnd = curPosEnd + (lenAfter - lenBefore);
		}
	});
	$(document).on('keydown', 'input[data-type="currency"]', function(ev) {
			let key = ev.originalEvent.key;
			switch (true) {
					case ('Tab' == key) && (ev.originalEvent.shiftKey):
					case 'Tab' == key:
					case 'End' == key:
					case 'Home' == key:
					case 'Delete' == key:
					case 'Backspace' == key:
					case 'ArrowRight' == key:
					case 'ArrowLeft' == key:
					case /\d/.test(key):
							break;
					default:
							ev.preventDefault();
			}
	});
	$(document).on('change', 'input[data-format="number"]', function() {
		let val = $(this).val();
		if (val.match(/[^0-9]/g)) {
			$(this).val('0');
		}
	});
	$(document).on('keydown', 'input[data-format="number"]', function(ev) {
		let key = ev.originalEvent.key;
		switch (true) {
				case ('Tab' == key) && (ev.originalEvent.shiftKey):
				case 'Tab' == key:
				case 'End' == key:
				case 'Home' == key:
				case 'Delete' == key:
				case 'Backspace' == key:
				case 'ArrowRight' == key:
				case 'ArrowLeft' == key:
				case 'ArrowUp' == key:
				case 'ArrowDown' == key:
				case /\d/.test(key):
						break;
				default:
						ev.preventDefault();
		}
	});

	$('#mnu-secondary').on('click', function(ev) {
			ev.preventDefault();
			if ($('#mnu-primary').css('display') == 'block') {
					$('#mnu-primary').css('display', 'none');
					$('#mnu-secondary').removeClass('menu-active');
			} else {
					$('#mnu-primary').css('display', 'block');
					$('#mnu-secondary').addClass('menu-active');
			}
	});
});
