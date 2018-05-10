var app = {

	initialize: function() {

		Parse.$ = jQuery;
        Parse.initialize('dVVty0n8MrhMhTusZHskFKJADY2HmG17KWW2TpQ9', 'ZauyN5aDZHeWgWp5W73U4qL6yCMm66Hf67QZNy5q');
        Parse.serverURL = 'https://parseapi.back4app.com';

		app.renderOrders();
	},

	renderOrders: function() {

		var Order = Parse.Object.extend("Order");
	    var query = new Parse.Query(Order);
	      
      	query.find({
        
	        success: function(results) {

	        	if (results.length > 0) {

	        		for (var i = 0; i < results.length; i++) {

			            var object = results[i];

			            var date = object.get("createdAt").toISOString().slice(0, 10);
			            var paymentStage = object.get("paymentStage");
			            var total = object.get("total");

			            var paymentState = '';

			            switch(paymentStage) {
						    case 1:
						        paymentState = '<span class="label label-warning">Pendiente</span>';
						        break;
						    case 2:
						        paymentState = '<span class="label label-success">Completado</span>';
						        break;
						    default:
						        paymentState = '<span class="label label-warning">Pendiente</span>';
						}

			            var item = '<tr>' +
	        						  '<td>' + date + '</td>' +
						              '<td>' + paymentState + '</td>' +
						              '<td>$' + total.toString() + '</td>' +
						            '</tr>';

						$('#tbody-items').append(item);

			        }

	        	} else {

	        		alert("No hay pedidos");

	        	}

	        },
	        error: function(error) {
	        	alert("Error, intente de nuevo por favor");
	        	console.log("Error: " + error.code + " " + error.message);
	        }
	      });

	}

}

app.initialize();