/**
 * Created by Администратор on 09.08.2016.
 */



$("#client").click(function(){
    $("#object-create").modal("show")
        .find("#modalContent")
        .load($(this).attr("value"));

});

$('#dynagrid-pjax').on('click', '#addProduct', function (e) {
		$("#product-create").modal("show")
        .find("#modalProduct")
        .load($(this).attr("value"));

 });
$("#guarantor").click(function(){
    $("#object-create").modal("show")
        .find("#modalContent")
        .load($(this).attr("value"));

});
function deletMonth(id, date)
{
    $("#object-create").modal("show")
        .find("#modalContent")
		.load('show-date?id='+id+"&date="+date);


}
$("#product").click(function(){
    $("#product-create").modal("show")
        .find("#modalContent")
        .load($(this).attr("value"));

});
function deleteMonthBtn(id)
{
  window.location.href = 'delete-month?id='+id+'&date='+$("#date").val();
}
function paymentPlan()
{
	 $.get('set-payment', {sum:$("#sum").val(),fee:$("#fee").val(),month:$("#month").val()},function(data) {
        $("#month_payment").val(data);
    });
	
}
// Обработка отправки формы через Ajax
$(document).on('click', '#saveProductButton', function (e) {
    e.preventDefault(); // отменяем стандартное поведение кнопки
    var form = $('#product-form');
    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        success: function (response) {
            if(response.success) {
                $('#product-create').modal('hide');
                $.pjax.reload({container:'#dynagrid-pjax'}); // обновляем таблицу
            } else {
                $('#modalContent').html(response.content); // валидация, если есть ошибки
            }
        },
        error: function () {
            alert('Ошибка при сохранении данных');
        }
    });
    return false; // отменяем стандартную отправку формы
});
$(document).on('click', '.delete-button', function (e) {
    e.preventDefault();
    var url = $(this).attr('href'); // берем ссылку для удаления

    if (confirm('Вы уверены, что хотите удалить эту запись?')) {
        $.ajax({
            url: url,
            type: 'post',
            success: function (data) {
                $.pjax.reload({container: '#dynagrid-pjax'});
            },
            error: function () {
                alert('Ошибка при удалении записи.');
            }
        });
    }
});
function paymentPlanMonth()
{
	 $.get('set-payment-month', {sum:$("#sum").val(),payment:$("#month_payment").val(),month:$("#month").val()},function(data) {
        $("#fee").val(data);
    });
	
}
function changeCommission()
{
	 var sum = ($("#commission").val()/ $("#sum").val())*100;
	 sum =  Math.round(sum);
	  $("#percant").val(sum);
	
	
}
function changeSum()
{
	 var sum = $("#commission").val() * $("#percant").val()/100;
	 sum =  Math.round(sum);
	  $("#month_payment").val(sum);
	 changeMonth()
	
}

function changeMonth()
{
	 var sum = $("#month_payment").val() * $("#month").val();
	 sum =  Math.round(sum);
	  $("#sum").val(sum);
	 
	
}
function printCredit(id) {
    $("<iframe>")                             // create a new iframe element
        .hide()                               // make it invisible
        .attr("src", "print?id="+id) // point the iframe to the page you want to print
        .appendTo("body");                    // add iframe to the DOM to cause it to load the page

}
function printAkt(id) {
    $("<iframe>")                             // create a new iframe element
        .hide()                               // make it invisible
        .attr("src", "akt?id="+id) // point the iframe to the page you want to print
        .appendTo("body");                    // add iframe to the DOM to cause it to load the page

}

function printAgreement(id) {
    $("<iframe>")                             // create a new iframe element
        .hide()                               // make it invisible
        .attr("src", "agreement?id="+id) // point the iframe to the page you want to print
        .appendTo("body");                    // add iframe to the DOM to cause it to load the page

}
function printStatement(id) {
    $("<iframe>")                             // create a new iframe element
        .hide()                               // make it invisible
        .attr("src", "statement?id="+id) // point the iframe to the page you want to print
        .appendTo("body");                    // add iframe to the DOM to cause it to load the page

}
$("#month_payment").change(function(){

  $("#date").prop('disabled',false);

})
function receivedCredit(id) {

var payment, month,fine; 
if ($("#sum").val() > 0)
    $.get('received-credit', {sum:$("#sum").val(),note:$("#note").val(),id_credit:id},function(data) {
		payment = data;

    });
if ($("#fine").val() > 0 )
	 $.get('payment-fine', {sum:$("#fine").val(),note:$("#note").val(),id_credit:id},function(data) {
		
		fine = data;
    });
if ($("#month_payment").val() > 0 )
	{
		 $.get('payment-month', {sum:$("#month_payment").val(),note:$("#note").val(),id_credit:id,date:$("#date").val()},function(data) {
			month = data;
			});

	}
	if (payment == undefined) payment="";
	if (fine == undefined) fine="";
	if (month == undefined) month="";
	setTimeout(function(){
		window.open("check?id="+id + "&payment="+ payment + "&month=" + month+ "&fine=" + fine);
		window.location.replace("view-credit?id="+id);
	},400);	
  /*  $("<iframe>")                             // create a new iframe element
        .hide()                       // make it invisible
        .attr("src", "check?id="+id) // point the iframe to the page you want to print
        .appendTo("body");   
		// add iframe to the DOM to cause it to load the page
		*/
		

}
function myFunction()
{
	alert("sdf");
	//window.location.replace("payment")
}