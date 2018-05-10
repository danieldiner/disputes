var app = {

	initialize: function() {

		Parse.$ = jQuery;
        Parse.initialize('dVVty0n8MrhMhTusZHskFKJADY2HmG17KWW2TpQ9', 'ZauyN5aDZHeWgWp5W73U4qL6yCMm66Hf67QZNy5q');
        Parse.serverURL = 'https://parseapi.back4app.com';

		app.renderDisputes();
	},

	renderDisputes: function() {

		var Dispute = Parse.Object.extend("Dispute");
	    var query = new Parse.Query(Dispute);
	      
      	query.find({
        
	        success: function(results) {

	        	if (results.length > 0) {

	        		for (var i = 0; i < results.length; i++) {

			            var object = results[i];

			            var date = object.get("createdAt").toISOString().slice(0, 10);
			            var disputeId = object.get("disputeId");
			            var transactionId = object.get("transactionId");
			            var sellerName = object.get("sellerName");
			            var buyerMail = object.get("buyerMail");
			            var reason = object.get("reason");
			            var buyerComment = object.get("buyerComment");
			            var status = object.get("status");
			            var outcome = object.get("outcome");

			          
			            var item = '<tr>' +
	        						  '<td>' + date + '</td>' +
						              '<td>' + disputeId + '</td>' +
						              '<td>' + transactionId + '</td>' +
						              '<td>' + sellerName + '</td>' +
						              '<td>' + buyerMail + '</td>' +
						              '<td>' + reason + '</td>' +
						              '<td>' + buyerComment + '</td>' +
						              '<td>' + status + '</td>' +
						              '<td>' + outcome + '</td>' +
						            '</tr>';

						$('#tbody-items').append(item);
			        }

	        	} else {

	        		alert("No hay disputas");

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