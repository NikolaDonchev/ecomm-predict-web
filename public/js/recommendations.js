(function() {

    // Function checking for DOM ready
    function ready(callback) {
		if (document.readyState === "complete"){
			callback();
		} else {
            document.addEventListener("DOMContentLoaded", callback);
        }
    }
    
    // sending a HTTP request to the server to get the recommendation
    function getRecommendation(customerId, elements) {
        var request = new XMLHttpRequest();
        request.open("POST", 'http://ecomm-predict.test/api/recommendations');
        request.setRequestHeader('Content-Type', 'application/json');
        // Checking if the customer is logged in
        if (customerId) {
            request.send(JSON.stringify({
                customer_id: customerId
            }));
        } else {
            request.send(null);
        }
        // When request is done parse the data to JSON
        // and pass it to updateContent
        request.onload = function(e) {
            responseJson = JSON.parse(request.responseText);
            updateContent(responseJson, elements);
        }
    }

    function updateContent(data, elements) {
        // Manipulating the DOM to add the recommended content
        elements.title.innerText = data.title;
        elements.image.src = data.image_url;
        elements.description.innerText = data.description;
        elements.button.innerText = data.button;
        elements.button.href = data.product_url;
        // Show the element
        elements.parent.style.opacity = 1;
    }

    ready(function() {

        // Set main container
        var container = document.querySelector('[data-recommendation-target]');
        // Get customer id from the platform
        var customerId = ShopifyAnalytics.meta.page.customerId;

        // List of child elements that will be updated
        var elements = {
            parent: container,
            image: container.querySelector('.hp-section-image'),
            title: container.querySelector('.hp-section-title'),
            description: container.querySelector('.hp-section-description'),
            button: container.querySelector('.hp-section-button')
        }

        // Checking if the customer id exists to send the API request
        if (customerId) {
            getRecommendation(customerId, elements);
        // If there is not customer id the PHP server
        // will return the most popular products
        } else {
            getRecommendation(null, elements);
        }

    });

})();