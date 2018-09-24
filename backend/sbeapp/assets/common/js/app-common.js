var SAPP = angular.module('appnSAPP', []);

gAPP.formHeaders = {'Content-Type': 'application/x-www-form-urlencoded'};

function isValidEmail(str) {
    var emPattern = /(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))/;
    return str.match(emPattern) !== null ? true : false;
}

function isHtmlResponse(r) {
    return checkResponse(r) == "html" ? true : false;
}

function isJsonResponse(r) {
    return checkResponse(r) == "json" ? true : false;
}

function checkResponse(r) {
    if (typeof r === "undefined") {
        return false;
    }
    if (r.indexOf('isJsonReturnType') > -1) {
        return 'json';
    }
    if (r.indexOf('html') > -1) {
        return 'html';
    }
}

function getHash() {
    return window.location.hash;
}

function getUrlParams() {
    var searchString = window.location.search.substring(1), params = searchString.split("&"), hash = {};
    if (searchString === "")
        return {};
    for (var i = 0; i < params.length; i++) {
        var val = params[i].split("=");
        hash[unescape(val[0])] = unescape(val[1]);
    }
    return hash;
}

function redirect(url, completeUrl) {
    var completeUrl = typeof completeUrl === "undefined" ? false : completeUrl;
    window.location.href = completeUrl === true ? url : sgGlobals.siteUrl + url;
} //end sgRedirect function

function scrollTo(selector, cbFunc) {
    $("html, body").animate({
        scrollTop: $(selector).offset().top + 'px'
    }, 700, function () {
        if (typeof cbFunc !== "undefined") {
            cbFunc.call();
        }
    });
}

function previous() {
    window.history.back();
}

$(document).ready(function () {
    $(".fnExpBlock").on("click", function () {
        var oTarget = $("#" + $(this).attr("data-targetId"));
        if (oTarget.css("display") == "none") {
            oTarget.slideDown();
        } else {
            oTarget.slideUp();
        }
    });
});

//common angular services
SAPP.service("commonServices",
        function ($http, $q) {

            return({
                getReferences: getReferences,
                getLists: getLists,
                getEventLists: getEventLists,
                doPreloader: doPreloader
            });

            function doPreloader(parentId, show) {
                var show = typeof show !== "undefined" ? show : true; //default show
                if (show === true) {
                    $("#" + parentId + " .preLoader").fadeIn();
                    $("#" + parentId + " .results").fadeOut();
                } else {
                    $("#" + parentId + " .preLoader").fadeOut();
                    $("#" + parentId + " .results").fadeIn();
                }
            }

            function getReferences(query) {
                var params = $.param(query);
                var request = $http({
                    method: "post",
                    url: gAPP.siteUrl + "reqs/getReferences",
                    headers: gAPP.formHeaders,
                    data: params
                });
//                var request = $http.post(gAPP.siteUrl+"reqs/checkEmail?email="+email, $.param({ email:'simcoury@yahoo.com' }));
                return(request.then(handleSuccess, handleError));
            }

            function getLists(url, query) {
                var request = $http({
                    method: "post",
                    url: url,
                    headers: gAPP.formHeaders,
                    data: $.param(query)
                });
                return(request.then(handleSuccess, handleError));
            }

            function getEventLists(url, query) {
                var request = $http({
                    method: "post",
                    url: url,
                    headers: gAPP.formHeaders,
                    data: $.param(query)
                });
                return(request.then(handleSuccess, handleError));
            }

            function handleError(response) {
                if (!angular.isObject(response.data) || !response.data.message) {
                    return($q.reject("An unknown error occurred."));
                }
                return($q.reject(response.data.message));
            }

            function handleSuccess(response) {
                return(response.data);
            }
        });
