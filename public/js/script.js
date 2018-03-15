function productivityReport()
{
	var from_date = document.getElementsByName("from-date")[0].value;
	var to_date = document.getElementsByName("to-date")[0].value;
	window.location = './mvc/controller/ProductivityController.php?' + 'from-date=' + from_date + '&to-date=' + to_date;
}

function operationReport()
{
	var from_date = document.getElementsByName("from-date")[0].value;
	var to_date = document.getElementsByName("to-date")[0].value;
	window.location = './mvc/controller/OperationController.php?' + 'from-date=' + from_date + '&to-date=' + to_date;
}