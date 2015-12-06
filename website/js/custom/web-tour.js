$(document).ready(function () {
    var tour = new Tour({
        name: "tour",
        steps: [
            {
                path: '/signup/help-demo',
                element: ".email-wrapper",
                title: "Email",
                placement: "bottom",
                content: "This should be a valid email address"
            },
            {
                path: '/signup/help-demo',
                element: ".card-wrapper",
                title: "Card information",
                placement: "bottom",
                content: "Valid credit/debit card number."
            },
            {
                path: '/signup/help-demo',
                element: ".cvc-wrapper",
                title: "CVC",
                placement: "left",
                content: "CVC of the card."
            },
            {
                path: '/signup/help-demo',
                element: ".exp-wrapper",
                title: "Expiration",
                placement: "right",
                content: "Expiraion month (eg: 07) of the card followed by expiration year (eg: 19)."
            },
            {
                path: '/signup/help-demo',
                element: ".address-wrapper",
                title: "Address",
                placement: "top",
                content: "Billing address of the card."
            },
            {
                path: '/signup/help-demo',
                element: ".checkout-wrapper",
                title: "Complete checkout",
                placement: "top",
                content: "Click this button after properly filling the above form."
            }
        ],
        container: "body",
        keyboard: true,
//        storage: window.localStorage,
        storage: false,
        debug: false,
        backdrop: false,
        backdropContainer: 'body',
        backdropPadding: 0,
        redirect: true,
        orphan: false,
        duration: false,
        delay: false,
        basePath: "",
        template: "<div class='popover tour'>" +
                    "<div class='arrow'></div>" +
                        "<h3 class='popover-title'></h3>" +
                        "<div class='popover-content'></div>" +
                        "<div class='popover-navigation'>" +
                            "<button class='btn btn-warning' data-role='prev'>« Prev</button>" +
                            "<span data-role='separator'>&nbsp;</span>" +
                            "<button class='btn btn-primary' data-role='next'> Next » </button>" +
                            "<span data-role='separator'>&nbsp;</span>" +
                            "<button class='btn btn-danger' data-role='end'>End tour</button>" +
                        "</div>" +
                    "</div>" +
                "</div>",
        afterGetState: function (key, value) {
        },
        afterSetState: function (key, value) {
        },
        afterRemoveState: function (key, value) {
        },
        onStart: function (tour) {
        },
        onEnd: function (tour) {
        },
        onShow: function (tour) {
        },
        onShown: function (tour) {
        },
        onHide: function (tour) {
        },
        onHidden: function (tour) {
        },
        onNext: function (tour) {
        },
        onPrev: function (tour) {
        },
        onPause: function (tour, duration) {
        },
        onResume: function (tour, duration) {
        },
        onRedirectError: function (tour) {
        }
    });


    $(document).off('click', '#start-tour').on('click', '#start-tour', function () {

        //Initialize the tour
        tour.init();
        //Start the tour
        tour.start();
    });

});

