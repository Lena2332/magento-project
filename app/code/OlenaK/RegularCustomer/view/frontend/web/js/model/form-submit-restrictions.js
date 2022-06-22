define([
    'ko'
], function (ko) {
    'use strict';

    let formSubmitDeniedRestrictions = {
        customerMustLogIn: ko.observable(false),
        requestAlreadySent: ko.observable(false)
    };

    formSubmitDeniedRestrictions.submitDenied = ko.computed(function () {
        return formSubmitDeniedRestrictions.customerMustLogIn() || formSubmitDeniedRestrictions.requestAlreadySent();
    });

    return formSubmitDeniedRestrictions;
});
