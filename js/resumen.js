var app = {

	initialize: function() {

		Parse.$ = jQuery;
        Parse.initialize('dVVty0n8MrhMhTusZHskFKJADY2HmG17KWW2TpQ9', 'ZauyN5aDZHeWgWp5W73U4qL6yCMm66Hf67QZNy5q');
        Parse.serverURL = 'https://parseapi.back4app.com';

		app.renderItemsCheckout();
	},

	renderItemsCheckout: function() {

		if (app.supportLocalStorage()) {
	        	
        	// Checks if cartArray already exists
        	if (localStorage.getItem("cartArray") != null) {

        		// cartArray already exists

        		// get cartArray from local storage 
        		var cartArrayString = localStorage.getItem("cartArray");

        		// parse cartArray so that we can manipulate it
        		var cartArray = JSON.parse(cartArrayString);

        		var total = 0;

        		var itemsArrayPaypal = [];

        		for (var i = 0; i < cartArray.length; i++) {

        			var subTotal = parseInt(cartArray[i].quantity) * parseInt(cartArray[i].price);
        			var total = total + subTotal;

        			var item = '<tr>' +
        						  '<td><img src="' + cartArray[i].imgUrl + '" height="80px"></td>' +		
					              '<td>' + cartArray[i].title + '</td>' +
					              '<td>' + cartArray[i].quantity + '</td>' +
					              '<td>$ ' + cartArray[i].price + '</td>' +
					              '<td>$ ' + subTotal + '</td>' +
					            '</tr>';

					var itemPaypal = {
                                      "name": cartArray[i].title,
                                      "price": cartArray[i].price,
                                      "currency": "MXN",
                                      "quantity": cartArray[i].quantity
                                    };

					$('#tbody-items').append(item);

					itemsArrayPaypal.push(itemPaypal);

        		}

        		var totalItem = '<tr>' +
        						  '<td></td>' +		
					              '<td></td>' +
					              '<td></td>' +
					              '<td><strong>TOTAL</strong></td>' +
					              '<td><strong>$ ' + total + '</strong></td>' +
					            '</tr>';

				$('#tbody-items').append(totalItem);

				app.renderPaypalButton(total,itemsArrayPaypal);

        	} else {

        		// cartArray does NOT exist yet

        		console.log("You don't have anything in the cart");
        	}

        } else {
        	alert("Ups! This browser does not support local storage. Please try with one of the following: Chrome,Firefox,Internet Explorer,Safari,Opera");
        }

	},

	renderPaypalButton: function(amountTotal,itemsArray) {

		var CREATE_PAYMENT_URL  = '../../server_services/create-payment.php';
      	var EXECUTE_PAYMENT_URL = '../../server_services/execute-payment.php';

		paypal.Button.render({

            env: 'sandbox', 

          	locale: 'es_MX',

	        style: {
	            label: 'pay',
	            size:  'large', 
	            shape: 'rect',   
	            color: 'silver'
	         },

          	client: {
             	sandbox: 'AS3GJc29BfOvxgWfFn1hp8_6zJaGmJrmzw4n6s5v3EaoxsWQfxObw82h57lgbUVH4pnNQmtyFQ0qfqkW' //PP
          	},

          	commit: true, // Show a 'Pay Now' button

            payment: function(data, actions) {
            	
            	var transactionsArray = {
		            "amount":
		            { 
		              "total": amountTotal,
		              "currency":"MXN"
		            }, 
		            "item_list":
		            {
		              "items": itemsArray
		            },
		            "description":"Compra desde Geenius Store"
		          };

		         // Set up the data you need to pass to your server
		        var dataArray = {
		        	transactions: JSON.stringify(transactionsArray)
		        };
		          
		        return paypal.request.post(CREATE_PAYMENT_URL, dataArray).then(function(data) {
		            console.log("Success - paymentID: " + data.paymentID);
		            return data.paymentID;
		        },function(error) {
		            console.log("Error: " + error);
		        });

            },
              
            onAuthorize: function(data, actions) {
  				
  				console.dir(data);

	         	// Set up the data you need to pass to your server
		        var dataArray = {
		            paymentID: data.paymentID,
		            payerID: data.payerID
		        };
	          
	          	return paypal.request.post(EXECUTE_PAYMENT_URL, dataArray).then(function(paymentDetailsExecute) {

	          		console.dir(paymentDetailsExecute);
	          		
	          		var parentPayment = paymentDetailsExecute.transactions[0].related_resources[0].sale.parent_payment;
	          		var state = paymentDetailsExecute.transactions[0].related_resources[0].sale.state;
	          		var total = paymentDetailsExecute.transactions[0].amount.total;

           			switch(state) {
					    case "completed":

					    	console.log("*** completed status ***");

					        var Order = Parse.Object.extend("Order");
						    var order = new Order();

						    order.set("parentPayment",parentPayment);
						    order.set("paymentStage",2);
						    order.set("total",parseInt(total));
						    
						    order.save(null, {
						      success: function(newOrder) {
						        alert("¡Pago completado, gracias por su compra!")
								localStorage.removeItem("cartArray");
								window.location = "index.php";
						      },
						      error: function(object, error) {
						        console.log("Error: " + error);
						      }
						    });

					        break;

					    case "partially_refunded":
					    	console.log("partially_refunded");
					        break;

					    case "pending":
					    	
					    	console.log("*** pendings status ***");

					        var Order = Parse.Object.extend("Order");
						    var order = new Order();

						    order.set("parentPayment",parentPayment);
						    order.set("paymentStage",1);
						    order.set("total",parseInt(total));
						    
						    order.save(null, {
						      success: function(newOrder) {
						        alert("Hemos recibido su pedido pero el pago está pendiente de procesar. Consulta el estado del pago en \"Mis pedidos\"");
								localStorage.removeItem("cartArray");
								window.location = "index.php";
						      },
						      error: function(object, error) {
						        console.log("Error: " + error);
						      }
						    });	

					        break;
					    case "refunded":
					    	console.log("refunded");
					        break;
					    case "denied":
					    	console.log("denied");
					        break;
					    default:
					        console.log("default");
					}
                            

		        },function(error) {
		            console.log("Error: " + error);
		        });

	         },

	        onCancel: function(data, actions) {
	            // Show a cancel page or return to cart
	            console.log("Error: " + data);
	        }

        }, '#paypal-button-container');

	},

	supportLocalStorage: function() {
		if(typeof(Storage) !== "undefined") {
			return true;
		} else {
			return false;
		}

	}

}

app.initialize();